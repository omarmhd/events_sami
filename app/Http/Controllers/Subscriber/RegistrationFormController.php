<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\RegistrationForm;
use App\Services\RegistrationFormService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegistrationFormController extends Controller
{
    public function __construct(
        private RegistrationFormService $registrationFormService,
        private SubscriptionService $subscriptionService,
    ) {
    }

    public function index(Request $request)
    {
        $user    = $request->user();
        $company = $user->company;

        $forms = RegistrationForm::query()
            ->where('company_id', $user->company_id)
            ->where('name', '!=', '__default__')   // hide auto-generated system form
            ->withCount('events')
            ->latest('id')
            ->paginate(12);

        // Feature gating info passed to view for UI feedback.
        $formsEnabled = $company
            ? $this->subscriptionService->featureEnabled($company, 'registration_forms')
            : true;

        $formsLimit = $company
            ? ($this->subscriptionService->featureLimit($company, 'registration_forms')
                ?? config('features.registration_forms.default_limit'))
            : null;

        $formsCount = $company
            ? RegistrationForm::where('company_id', $user->company_id)->count()
            : 0;

        return view('subscriber.events.forms.index', [
            'forms'        => $forms,
            'formsEnabled' => $formsEnabled,
            'formsLimit'   => $formsLimit,   // null = unlimited
            'formsCount'   => $formsCount,
            'canCreate'    => $formsEnabled && ($formsLimit === null || $formsCount < $formsLimit),
        ]);
    }

    public function create(Request $request)
    {
        $user    = $request->user();
        $company = $user->company;

        // Block access to create form if feature is disabled or limit reached.
        if ($company) {
            $enabled = $this->subscriptionService->featureEnabled($company, 'registration_forms');
            if (!$enabled) {
                return redirect()->route('feature.unavailable', ['feature' => 'registration_forms']);
            }

            $limit = $this->subscriptionService->featureLimit($company, 'registration_forms')
                ?? config('features.registration_forms.default_limit');

            if ($limit !== null) {
                $count = RegistrationForm::where('company_id', $user->company_id)->count();
                if ($count >= $limit) {
                    return redirect()->route('registration-forms.index')
                        ->with('error', config('features.registration_forms.limit_reached_message'));
                }
            }
        }

        return view('subscriber.events.forms.registration-form', [
            'formModel' => new RegistrationForm([
                'company_id' => $user->company_id,
                'is_active' => true,
            ]),
            'mode' => 'create',
            'fieldTypes' => RegistrationFormService::FIELD_TYPES,
            'fieldWidths' => RegistrationFormService::FIELD_WIDTHS,
        ]);
    }

    public function store(Request $request)
    {
        $user    = $request->user();
        $company = $user->company;

        // Re-check server-side — form could be submitted directly.
        if ($company) {
            $enabled = $this->subscriptionService->featureEnabled($company, 'registration_forms');
            if (!$enabled) {
                abort(403, 'ميزة نماذج التسجيل غير متاحة في خطتك الحالية.');
            }

            $limit = $this->subscriptionService->featureLimit($company, 'registration_forms')
                ?? config('features.registration_forms.default_limit');

            if ($limit !== null) {
                $count = RegistrationForm::where('company_id', $user->company_id)->count();
                if ($count >= $limit) {
                    return redirect()->route('registration-forms.index')
                        ->with('error', config('features.registration_forms.limit_reached_message'));
                }
            }
        }

        $data = $this->validateRequest($request, $user->company_id);

        RegistrationForm::create([
            'organization_id' => $user->organization_id ?: $user->company_id,
            'company_id'      => $user->company_id,
            'created_by'      => $user->id,
            'name'            => $data['name'],
            'slug'            => $this->generateUniqueSlug($data['name'], $user->company_id),
            'headline'        => $data['headline'] ?? null,
            'intro_text'      => $data['intro_text'] ?? null,
            'fields'          => $data['fields'],
            'is_active'       => $data['is_active'],
        ]);

        return redirect()->route('registration-forms.index')
            ->with('success', 'Registration form created successfully.');
    }

    public function edit(Request $request, RegistrationForm $registrationForm)
    {
        $this->authorizeForm($request, $registrationForm);

        return view('subscriber.events.forms.registration-form', [
            'formModel' => $registrationForm,
            'mode' => 'edit',
            'fieldTypes' => RegistrationFormService::FIELD_TYPES,
            'fieldWidths' => RegistrationFormService::FIELD_WIDTHS,
        ]);
    }

    public function update(Request $request, RegistrationForm $registrationForm)
    {
        $this->authorizeForm($request, $registrationForm);

        $data = $this->validateRequest($request, $registrationForm->company_id, $registrationForm->id);

        // Regenerate slug only when the name changed to avoid breaking existing links.
        $newSlug = $data['name'] !== $registrationForm->name
            ? $this->generateUniqueSlug($data['name'], $registrationForm->company_id, $registrationForm->id)
            : $registrationForm->slug;

        $registrationForm->update([
            'name'       => $data['name'],
            'slug'       => $newSlug,
            'headline'   => $data['headline'] ?? null,
            'intro_text' => $data['intro_text'] ?? null,
            'fields'     => $data['fields'],
            'is_active'  => $data['is_active'],
        ]);

        return redirect()->route('registration-forms.index')
            ->with('success', 'Registration form updated successfully.');
    }

    public function destroy(Request $request, RegistrationForm $registrationForm)
    {
        $this->authorizeForm($request, $registrationForm);

        if ($registrationForm->events()->exists()) {
            return redirect()->route('registration-forms.index')
                ->with('error', 'This form is linked to events. Unlink it before deleting.');
        }

        $registrationForm->delete();

        return redirect()->route('registration-forms.index')
            ->with('success', 'Registration form deleted successfully.');
    }

    protected function validateRequest(Request $request, int $companyId, ?int $formId = null): array
    {
        $rawFields = json_decode((string) $request->input('fields_json', '[]'), true);
        $normalizedFields = $this->registrationFormService->normalizeFields($rawFields);

        $data = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'headline'   => ['nullable', 'string', 'max:160'],
            'intro_text' => ['nullable', 'string'],
            'is_active'  => ['nullable', 'boolean'],
        ]);

        $data['fields']    = $normalizedFields;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    /**
     * Generate a unique slug from a form name within the same company.
     * Appends a numeric suffix when collisions occur (e.g. my-form-2).
     */
    protected function generateUniqueSlug(string $name, int $companyId, ?int $ignoreId = null): string
    {
        $base   = Str::slug($name) ?: 'form';
        $slug   = $base;
        $suffix = 2;

        while (
            RegistrationForm::where('slug', $slug)
                ->where('company_id', $companyId)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    protected function authorizeForm(Request $request, RegistrationForm $registrationForm): void
    {
        if ((int) $registrationForm->company_id !== (int) $request->user()->company_id) {
            abort(403);
        }

        // Prevent editing/deleting the auto-generated default form.
        if ($registrationForm->name === '__default__') {
            abort(403, 'هذا النموذج محجوز للنظام ولا يمكن تعديله.');
        }
    }
}


