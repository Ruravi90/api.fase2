<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class AdminPromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promoCodes = PromoCode::with('plans')->latest()->get();
        return response()->json($promoCodes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'required|date',
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:plans,id'
        ]);

        $promoCode = PromoCode::create([
            'code' => $validated['code'],
            'percentage' => $validated['percentage'],
            'is_active' => $validated['is_active'] ?? true,
            'max_uses' => $validated['max_uses'],
            'expires_at' => $validated['expires_at'],
        ]);

        $promoCode->plans()->sync($validated['plan_ids']);

        return response()->json($promoCode->load('plans'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promoCode = PromoCode::with('plans')->findOrFail($id);
        return response()->json($promoCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $promoCode = PromoCode::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|unique:promo_codes,code,' . $promoCode->id,
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'required|date',
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:plans,id'
        ]);

        $promoCode->update([
            'code' => $validated['code'],
            'percentage' => $validated['percentage'],
            'is_active' => $validated['is_active'] ?? true,
            'max_uses' => $validated['max_uses'],
            'expires_at' => $validated['expires_at'],
        ]);

        $promoCode->plans()->sync($validated['plan_ids']);

        return response()->json($promoCode->load('plans'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promoCode = PromoCode::findOrFail($id);
        $promoCode->plans()->detach();
        $promoCode->delete();
        return response()->json(['message' => 'Código eliminado correctamente']);
    }
}
