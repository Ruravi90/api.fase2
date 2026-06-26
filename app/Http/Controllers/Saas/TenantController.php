<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index()
    {
        return Tenant::all();
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
        
        $subscription = new \App\Models\Subscription();
        $subscription->tenant_id = $tenant->id;
        $subscription->plan_id = $request->plan_id;
        $subscription->status = 'active';
        $subscription->starts_at = now();
        $subscription->ends_at = now()->addMonths($request->months);
        $subscription->save();

        return response()->json([
            'message' => 'Plan asignado exitosamente',
            'subscription' => $subscription
        ]);
    }
}
