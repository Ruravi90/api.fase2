<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('features')->get();
        \Log::info("PlanController@index called by user " . auth()->id() . ". Found plans: " . count($plans));
        return response()->json([
            'plans' => $plans,
            'features' => Feature::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'features' => 'array',
            'features.*' => 'exists:features,id'
        ]);

        DB::beginTransaction();
        try {
            $plan = Plan::create($request->only(['name', 'price', 'currency', 'billing_cycle']));
            
            if ($request->has('features')) {
                $plan->features()->attach($request->features);
            }

            DB::commit();
            return response()->json(['message' => 'Plan creado', 'plan' => $plan->load('features')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        return Plan::with('features')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'features' => 'array',
            'features.*' => 'exists:features,id'
        ]);

        DB::beginTransaction();
        try {
            $plan->update($request->only(['name', 'price', 'currency', 'billing_cycle']));
            
            if ($request->has('features')) {
                $plan->features()->sync($request->features);
            }

            DB::commit();
            return response()->json(['message' => 'Plan actualizado', 'plan' => $plan->load('features')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->features()->detach();
        $plan->delete();

        return response()->json(['message' => 'Plan eliminado']);
    }
}
