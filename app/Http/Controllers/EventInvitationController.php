<?php

namespace App\Http\Controllers;

use Throwable;

use Illuminate\Support\Facades\Log;
use App\Mail\InvitationSent;
use App\Mail\TicketDetailsMail;
use App\Mail\TicketMail;
use App\Models\Event;
use App\Models\EventInvitation;
use App\Models\InvitationQr;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class EventInvitationController extends Controller
{

    public function index(Request $request)
    {
        $query = EventInvitation::query();

        if ($request->filled('searchInput')) {
            $search = $request->searchInput;

            $query->where(function ($q) use ($search) {
                $q->where('invitee_name', 'like', "%$search%")
                    ->orWhere('invitee_email', 'like', "%$search%")
                    ->orWhere('invitee_phone', 'like', "%$search%")
                    ->orWhere('invitee_position', 'like', "%$search%")
                    ->orWhere('invitee_nationality', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%");

            });
        }
        $stats = (clone $query)
            ->selectRaw("
        COUNT(*) as all_count,
        SUM(status = 'pending') as pending,
        SUM(status = 'accepted') as accepted,
        SUM(status = 'declined') as declined,
        SUM(status = 'maybe') as maybe
    ") ->first();;
        $rows = $query->latest()->paginate(6);

        $stats = [
            'all'      => $stats->all_count,
            'pending'  => $stats->pending,
            'accepted' => $stats->accepted,
            'declined' => $stats->declined,
            'maybe' => $stats->maybe,

        ];

        $event=Event::where("name","!=",null)->first();

        return view('admin.invitations.index', compact('rows','stats','event'));
    }

    public function create(){
        return view("admin.invitations.create");

    }
    public function store(Request $request)
    {
        $request->validate([

            'invitee_name'       => 'required|string|max:255',
            'invitee_email' => 'required|email|unique:event_invitations,invitee_email',            'invitee_position'   => 'nullable|string|max:255',
            'invitee_nationality'=> 'nullable|string|max:255',
            'allowed_guests'     => 'required|integer|min:0|max:10',
        ]);

        $invitation = EventInvitation::create([
            'company_id'          => null,
            'event_id'            => null,
            'invitee_name'        => $request->invitee_name,
            'invitee_email'       => $request->invitee_email,
            'invitee_position'    => $request->invitee_position,
            'invitee_nationality' => $request->invitee_nationality,
            'allowed_guests'      => $request->allowed_guests,
            'invitation_token'    => Str::uuid(),
        ]);

        $invitationLink = route(
            'rsvp.show',
            $invitation->invitation_token
        );
        $event=Event::where("name","!=",null)->first();

        if ($invitation->invitee_email) {
            Mail::to($invitation->invitee_email)
                ->send(new InvitationSent($invitation, $invitationLink,$event));
        }

        return redirect()
            ->back()
            ->with('success', 'Invitation sent successfully');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'id'       => 'integer'
        ]);
        $invitation = EventInvitation::findOrFail($request->id);

        $invitation->update([
            'status'           => 'pending',
            'responded_at'     => null,
            'selected_guests'  => 0,
            'invitation_token' => Str::uuid(),
        ]);

        $invitationLink = route(
            'rsvp.show',
            $invitation->invitation_token
        );
        $event=Event::where("name","!=",null)->first();
        InvitationQr::where("event_invitation_id",$invitation->id)->delete();

        if ($invitation->invitee_email) {
            Mail::to($invitation->invitee_email)
                ->send(new InvitationSent($invitation, $invitationLink,$event));
        }

        return redirect()
            ->back()
            ->with('success', 'Invitation resent successfully');
    }

    public function showByToken($token)
    {

        $event=\Illuminate\Support\Facades\DB::table("events")->where("name","SAMI-AEC")->first();

        $guest = EventInvitation::where('invitation_token', $token)
            ->firstOrFail();

        return view('index', compact('guest',"event"));
    }



    public function submit(Request $request, string $token, QrCodeService $qrService)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Debug Log (مؤقت)
            |--------------------------------------------------------------------------
            */
            Log::info('RSVP DEBUG', [
                'token' => $token,
                'request' => $request->all(),
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Get Invitation
            |--------------------------------------------------------------------------
            */
            $guest = EventInvitation::where('invitation_token', $token)->firstOrFail();

            if ($guest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم الرد على هذه الدعوة مسبقًا'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */
            $validator = Validator::make($request->all(), [
                'response_status' => 'required|in:accepted,declined,maybe',
                'guests_count' => 'nullable|integer|min:0|max:' . $guest->allowed_guests,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Update Invitation
            |--------------------------------------------------------------------------
            */
            $guest->update([
                'status' => $request->response_status,
                'responded_at' => now(),
                'selected_guests' => $request->response_status === 'accepted'
                    ? ($request->guests_count ?? 0)
                    : 0,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tickets + Email
            |--------------------------------------------------------------------------
            */
            if (in_array($request->response_status, ['accepted', 'maybe'])) {

                $tickets = [];

                for ($i = 0; $i <= $guest->selected_guests; $i++) {

                    $qrToken = (string) \Str::uuid();
                    $type = $i === 0 ? 'Main' : 'Guest';

                    InvitationQr::create([
                        'event_invitation_id' => $guest->id,
                        'type' => strtolower($type),
                        'token' => $qrToken,
                    ]);

                    $tickets[] = [
                        'label' => $type,
                        'qr' => $qrService->generateBase64($qrToken),
                    ];
                }

                $event = Event::where('name',"SAMI-AEC")->first();

                \Mail::to($guest->invitee_email)
                    ->send(new TicketMail($guest, $tickets, $event));
            }

            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully'
            ]);

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Catch ANY error (مهم)
            |--------------------------------------------------------------------------
            */
            Log::error('RSVP ERROR', [
                'token' => $token,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function submit_OLD(Request $request, $token,QrCodeService $qrService)
        {

            \Log::info('RSVP DEBUG', [
                'token' => $token,
                'request' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $guest = EventInvitation::where('invitation_token', $token)->firstOrFail();
            if ($guest->status !== 'pending') {
                return response()->json([
                    'message' => 'تم الرد على هذه الدعوة مسبقًا'
                ], 422);
            }
            $validator = Validator::make($request->all(), [
                'response_status' => 'required|in:accepted,declined,maybe',
                'guests_count' => 'nullable|integer|min:0|max:' . $guest->allowed_guests,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }


            $guest->update([
                'status' => $request->response_status,
                'responded_at'=>Carbon::now(),
                'selected_guests' => $request->response_status == 'accepted' ? ($request->guests_count ?? 0) : 0,
            ]);

            if($request->response_status == 'accepted' or $request->response_status == 'maybe'){

            $tickets = [];

            for ($i = 0; $i <= $guest->selected_guests; $i++) {

                $qrToken = Str::uuid()->toString();

                $type = $i === 0
                    ? 'Main'
                    :'Guest';
                $tickets[] = [
                    'label' => $type,
                    'qr' => $qrService->generateBase64($qrToken),
                ];

                $qr = InvitationQr::create([
                    'event_invitation_id' => $guest->id,
                    'type'  => strtolower($type),
                    'token' => $qrToken,
                ]);
            }

                $event=Event::where("name","!=",null)->first();

            Mail::to($guest->invitee_email)
                ->send(new TicketMail($guest, $tickets,$event));
            }


            return response()->json([
                'success' => true,
                'message' => 'Response saved successfully'
            ]);
    }

    public function destroy($id)
    {
        InvitationQr::where('event_invitation_id', $id)->delete();

        EventInvitation::findOrFail($id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Invitation deleted successfully');

    }


}
