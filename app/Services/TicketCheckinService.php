<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketCheckinLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketCheckinService
{
    public function checkIn(string $token, User $validator): array
    {
        $userOrganizationId = $validator->organization_id ?: $validator->company_id;

        $ticket = Ticket::query()
            ->with('event')
            ->where('token', $token)
            ->first();

        if (!$ticket) {
            $this->log($userOrganizationId, null, null, $validator->id, 'invalid', $token, ['reason' => 'ticket_not_found']);

            return [
                'status' => 'invalid',
                'message' => 'Invalid ticket.',
            ];
        }

        if (!$validator->isSystemAdmin() && (int) $ticket->organization_id !== (int) $userOrganizationId) {
            $this->log($ticket->organization_id, $ticket->event_id, $ticket->id, $validator->id, 'invalid', $token, ['reason' => 'cross_tenant']);

            return [
                'status' => 'invalid',
                'message' => 'Invalid ticket.',
            ];
        }

        $allowReentry = (bool) optional($ticket->event)->allow_reentry;
        $alreadyUsed = $ticket->checked_in_count > 0 || $ticket->checked_in_at !== null;

        if ($alreadyUsed && !$allowReentry) {
            $this->log($ticket->organization_id, $ticket->event_id, $ticket->id, $validator->id, 'already_used', $token);

            return [
                'status' => 'already_used',
                'message' => 'Ticket already used.',
                'ticket' => $ticket,
            ];
        }

        DB::transaction(function () use ($ticket, $validator, $token) {
            $ticket->forceFill([
                'checked_in_at' => Carbon::now(),
                'checked_in_count' => ((int) $ticket->checked_in_count) + 1,
                'status' => 'checked_in',
            ])->save();

            $this->log($ticket->organization_id, $ticket->event_id, $ticket->id, $validator->id, 'accepted', $token);
        });

        return [
            'status' => 'accepted',
            'message' => 'Check-in accepted.',
            'ticket' => $ticket->fresh(),
        ];
    }

    protected function log(?int $organizationId, ?int $eventId, ?int $ticketId, ?int $validatedBy, string $result, ?string $token, array $meta = []): void
    {
        if ($organizationId === null) {
            return;
        }

        TicketCheckinLog::query()->create([
            'organization_id' => $organizationId,
            'event_id' => $eventId,
            'ticket_id' => $ticketId,
            'validated_by' => $validatedBy,
            'result' => $result,
            'scanned_token' => $token,
            'meta' => $meta,
        ]);
    }
}
