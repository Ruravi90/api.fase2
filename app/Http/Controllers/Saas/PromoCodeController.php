<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $promoCode = PromoCode::where('code', $request->code)->first();

        if (!$promoCode) {
            return response()->json(['valid' => false, 'message' => 'Código no encontrado'], 404);
        }

        if (!$promoCode->is_active) {
            return response()->json(['valid' => false, 'message' => 'El código no está activo'], 400);
        }

        if ($promoCode->expires_at && $promoCode->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'El código ha expirado'], 400);
        }

        if ($promoCode->max_uses !== null && $promoCode->uses_count >= $promoCode->max_uses) {
            return response()->json(['valid' => false, 'message' => 'El código ha alcanzado su límite de usos'], 400);
        }

        // Verify if it applies to the given plan
        $appliesToPlan = $promoCode->plans()->where('plan_id', $request->plan_id)->exists();
        if (!$appliesToPlan) {
            return response()->json(['valid' => false, 'message' => 'El código no es válido para el plan seleccionado'], 400);
        }

        return response()->json([
            'valid' => true,
            'percentage' => $promoCode->percentage,
            'promo_code_id' => $promoCode->id
        ]);
    }
}
