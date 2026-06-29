<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index()
    {
        return Tenant::with('subscription.plan')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants',
            'admin_name' => 'required|string|max:255',
            'admin_username' => 'required|string|max:255|unique:users,username',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $tenant = new Tenant();
            $tenant->name = $request->name;
            $tenant->domain = $request->domain;
            // force saving ignoring any guarded rules
            $tenant->save();

            // Set tenant ID manually to circumvent auto-scoping issues
            $user = new User();
            $user->name = $request->admin_name;
            $user->username = $request->admin_username;
            $user->email = $request->admin_email;
            $user->password = Hash::make($request->admin_password);
            $user->lastname = '';
            $user->motherlastname = '';
            $user->phone_mobile = '';
            $user->tenant_id = $tenant->id;
            $user->save();

            $user->assignRole('admin');

            DB::commit();

            return response()->json([
                'message' => 'Clínica y administrador creados exitosamente',
                'tenant' => $tenant
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        return Tenant::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $tenant->id,
        ]);

        $tenant->update($request->only(['name', 'domain']));

        return response()->json(['message' => 'Clínica actualizada', 'tenant' => $tenant]);
    }

    public function destroy(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        return response()->json(['message' => 'Clínica eliminada']);
    }

    public function assignPlan(Request $request, string $id)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'months' => 'required|integer|min:1'
        ]);

        $tenant = Tenant::findOrFail($id);
        
        $subscription = \App\Models\Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $request->plan_id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonths($request->months)
            ]
        );

        return response()->json([
            'message' => 'Plan asignado exitosamente',
            'subscription' => $subscription
        ]);
    }

    public function publicRegister(Request $request)
    {
        $request->validate([
            'tenant_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            // Create Tenant
            $tenant = new Tenant();
            $tenant->name = $request->tenant_name;
            $tenant->domain = $request->domain;
            $tenant->save();

            // Create Admin User
            $user = new User();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->lastname = '';
            $user->motherlastname = '';
            $user->phone_mobile = '';
            $user->tenant_id = $tenant->id;
            $user->save();

            // Assign Admin Role
            $user->assignRole('admin');

            DB::commit();

            // Automatically login the user
            \Illuminate\Support\Facades\Auth::guard('web')->login($user);
            $request->session()->regenerate();

            // Load roles for response payload
            $user->loadMissing('roles');
            $success = $user->makeHidden(['password', 'remember_token'])->toArray();

            return response()->json(['success' => $success], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
