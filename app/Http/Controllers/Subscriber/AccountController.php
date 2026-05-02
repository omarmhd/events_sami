<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Subscriber Account Settings
 *
 * صفحة إدارة حساب المستخدم تتضمن ثلاث تبويبات:
 *   1. حسابي    : الاسم + رقم الجوال (متاح للجميع)
 *   2. الأمان   : تغيير البريد الإلكتروني وكلمة المرور
 *                  (يتطلب تأكيد كلمة المرور الحالية)
 *   3. المؤسسة : بيانات الشركة (الاسم، الهاتف، البريد، النطاق الفرعي،
 *                  المنطقة الزمنية) — تظهر فقط لمالك المؤسسة (owner).
 */
class AccountController extends Controller
{
    /**
     * عرض صفحة إدارة الحساب.
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $company = $user->company;
        $isOwner = $company && (int) $company->owner_user_id === (int) $user->id;

        return view('subscriber.panel.account', [
            'user'    => $user,
            'company' => $company,
            'isOwner' => $isOwner,
        ]);
    }

    /**
     * تحديث البيانات الشخصية الأساسية: الاسم ورقم الجوال.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($data);

        return redirect()
            ->route('account.index', ['tab' => 'profile'])
            ->with('success', __('ui.account.flash.profile_updated'));
    }

    /**
     * تحديث البريد الإلكتروني — يتطلب تأكيد كلمة المرور الحالية لمنع
     * أي تغيير غير مصرَّح به في حال ترك الجلسة مفتوحة.
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'email'            => [
                'required', 'email', 'max:160',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'email_confirmation' => ['required', 'same:email'],
            'current_password' => ['required', 'string'],
        ], [], [
            'email' => __('ui.account.field.email'),
            'email_confirmation' => __('ui.account.field.email_confirmation'),
            'current_password' => __('ui.account.field.current_password'),
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => __('ui.account.errors.current_password_invalid')])
                ->withInput()
                ->with('active_tab', 'security');
        }

        $user->forceFill([
            'email'             => strtolower(trim($data['email'])),
            'email_verified_at' => null,
        ])->save();

        return redirect()
            ->route('account.index', ['tab' => 'security'])
            ->with('success', __('ui.account.flash.email_updated'));
    }

    /**
     * تحديث كلمة المرور — كلمة المرور الحالية + الجديدة + التأكيد.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'current_password' => __('ui.account.field.current_password'),
            'password'         => __('ui.account.field.new_password'),
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => __('ui.account.errors.current_password_invalid')])
                ->withInput()
                ->with('active_tab', 'security');
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return redirect()
            ->route('account.index', ['tab' => 'security'])
            ->with('success', __('ui.account.flash.password_updated'));
    }

    /**
     * تحديث بيانات المؤسسة — يقتصر على مالك الشركة (owner) فقط.
     */
    public function updateCompany(Request $request)
    {
        $user    = $request->user();
        $company = $user->company;

        if (!$company) {
            abort(404);
        }

        // Only the company's owner_user_id can edit organization-wide data.
        if ((int) $company->owner_user_id !== (int) $user->id) {
            abort(403, __('ui.account.errors.forbidden_company'));
        }

        $data = $request->validate([
            'company_name'   => ['required', 'string', 'max:160'],
            'contact_email'  => ['nullable', 'email', 'max:160'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'subdomain'      => [
                'required', 'alpha_dash', 'min:3', 'max:40',
                Rule::unique('companies', 'subdomain')->ignore($company->id),
            ],
            'timezone'       => ['nullable', 'string', 'max:64'],
        ], [], [
            'company_name'  => __('ui.account.field.company_name'),
            'contact_email' => __('ui.account.field.company_email'),
            'phone'         => __('ui.account.field.company_phone'),
            'subdomain'     => __('ui.account.field.subdomain'),
            'timezone'      => __('ui.account.field.timezone'),
        ]);

        $company->update([
            'name'          => $data['company_name'],
            'contact_email' => $data['contact_email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'subdomain'     => strtolower(trim($data['subdomain'])),
            'timezone'      => $data['timezone'] ?? $company->timezone,
        ]);

        return redirect()
            ->route('account.index', ['tab' => 'company'])
            ->with('success', __('ui.account.flash.company_updated'));
    }
}
