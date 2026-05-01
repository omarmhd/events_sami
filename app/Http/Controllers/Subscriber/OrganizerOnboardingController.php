<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

use App\Actions\Onboarding\CreateCompanyAction;
use App\Actions\Onboarding\UpdateCompanyProfileAction;
use App\Mail\OtpCodeMail;
use App\Models\Company;
use App\Models\OtpVerification;
use App\Models\User;
use App\Services\SubscriptionManagementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrganizerOnboardingController extends Controller
{
    public function __construct(
        private SubscriptionManagementService $subscriptionService,
        private CreateCompanyAction $createCompanyAction,
        private UpdateCompanyProfileAction $updateProfileAction,
    ) {}

    /**
     * Show registration page
     */
    public function showRegistrationForm()
    {
        return view('subscriber.onboarding.register');
    }

    /**
     * Send OTP to email
     */
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP
        OtpVerification::updateOrCreate(
            ['email' => $validated['email']],
            [
                'otp' => bcrypt($otp),
                'expires_at' => Carbon::now()->addMinutes(15),
                'attempts' => 0,
            ]
        );

        // Send OTP email immediately for individual onboarding requests
        Mail::to($validated['email'])->send(new OtpCodeMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني',
            'email' => $validated['email'],
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = OtpVerification::where('email', $validated['email'])->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح أو انتهى',
            ], 422);
        }

        if (Carbon::now()->isAfter($otpRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'انتهى الوقت المسموح به للرمز',
            ], 422);
        }

        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'عدد محاولات فاشلة كثيرة. يرجى طلب رمز جديد',
            ], 422);
        }

        if (!password_verify($validated['otp'], $otpRecord->otp)) {
            $otpRecord->increment('attempts');
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح',
            ], 422);
        }

        // Create user
        $user = User::create([
            'email' => $validated['email'],
            'name' => $validated['email'],
            'password' => bcrypt(Str::random(32)),
        ]);

        // Delete OTP record
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح',
            'user_id' => $user->id,
            'next_step' => 'profile_setup',
        ]);
    }

    /**
     * Show profile setup form
     */
    public function showProfileSetup()
    {
        return view('subscriber.onboarding.profile-setup');
    }

    /**
     * Store profile information and create company
     */
    public function storeProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'subdomain' => 'required|string|max:50|unique:companies,subdomain',
            'annual_events_estimate' => 'required|integer|min:1|max:1000',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            // Update user
            $user->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]);

            // Create company
            $company = ($this->createCompanyAction)(
                name: $validated['company_name'],
                subdomain: $validated['subdomain'],
                ownerUserId: $user->id,
                annualEventsEstimate: $validated['annual_events_estimate'],
            );

            // Attach user to company
            $user->update(['company_id' => $company->id]);

            // Initialize free trial
            $this->subscriptionService->initializeFreeTrial($company);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إعداد ملفك الشخصي بنجاح',
                'redirect' => route('organizer.dashboard'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show needs assessment form
     */
    public function showNeedsAssessment()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('onboarding.register');
        }

        return view('subscriber.onboarding.needs-assessment', ['company' => $company]);
    }

    /**
     * Store needs assessment and recommend plan
     */
    public function storeNeedsAssessment(Request $request)
    {
        $validated = $request->validate([
            'annual_events_estimate' => 'required|integer|min:1',
            'average_attendance' => 'required|integer|min:1',
            'requires_custom_development' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $company = auth()->user()->company;

        $assessment = $company->needsAssessment()->create([
            ...$validated,
            'assessed_at' => now(),
        ]);

        $recommendedPlan = $this->subscriptionService->recommendPlan($assessment);
        $assessment->update(['recommended_plan' => $recommendedPlan]);

        return response()->json([
            'success' => true,
            'recommended_plan' => $recommendedPlan,
            'message' => 'تم تقييم احتياجاتك بنجاح',
            'redirect' => route('subscription.upgrade', ['plan' => $recommendedPlan]),
        ]);
    }
}
