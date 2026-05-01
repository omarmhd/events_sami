<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->isSystemAdmin() && !$request->user()->company_id) {
            return redirect()->route('system.users');
        }

        $companyId = $request->user()->company_id;

        $users = User::query()
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->paginate(15);

        return view('subscriber.team.index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->user()->company_id) {
            return back()->with('error', __('ui.team.error_no_company'));
        }

        $companyId = $request->user()->company_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['organizer_admin', 'operator', 'validator', 'viewer'])],
            'password' => ['required', 'string', 'min:8', 'max:120'],
        ]);

        User::create([
            'company_id'      => $companyId,
            'name'            => $data['name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'] ?? null,
            'role'            => $data['role'],
            'password'        => Hash::make($data['password']),
            'is_system_admin' => false,
        ]);

        return back()->with('success', __('ui.team.success_created'));
    }

    public function updateRole(Request $request, User $member)
    {
        $companyId = $request->user()->company_id;

        if ((int) $member->company_id !== (int) $companyId) {
            abort(403);
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(['organizer_admin', 'operator', 'validator', 'viewer'])],
        ]);

        $member->update([
            'role' => $data['role'],
        ]);

        return back()->with('success', __('ui.team.success_role_updated'));
    }

    public function destroy(Request $request, User $member)
    {
        $companyId = $request->user()->company_id;

        if ((int) $member->company_id !== (int) $companyId) {
            abort(403);
        }

        if ((int) $member->id === (int) $request->user()->id) {
            return back()->with('error', __('ui.team.error_own_account'));
        }

        $member->delete();

        return back()->with('success', __('ui.team.success_removed'));
    }
}



