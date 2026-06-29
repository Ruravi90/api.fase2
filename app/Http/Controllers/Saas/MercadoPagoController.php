<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\PromoCode;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-000000000000-xxxx-xxxx'));
        // Puedes agregar más configuración aquí si es necesario (ej: runtime environment)
    }

    public function createPreference(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'promo_code_id' => 'nullable|exists:promo_codes,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $unitPrice = (float) $plan->price;
        $promoCodeIdToSave = '';

        if ($request->filled('promo_code_id')) {
            $promoCode = PromoCode::find($request->promo_code_id);
            if ($promoCode && $promoCode->is_active && (!$promoCode->expires_at || !$promoCode->expires_at->isPast())) {
                if ($promoCode->max_uses === null || $promoCode->uses_count < $promoCode->max_uses) {
                    $appliesToPlan = $promoCode->plans()->where('plan_id', $plan->id)->exists();
                    if ($appliesToPlan) {
                        $discount = $unitPrice * ($promoCode->percentage / 100);
                        $unitPrice = $unitPrice - $discount;
                        if ($unitPrice < 0) $unitPrice = 0;
                        $promoCodeIdToSave = $promoCode->id;
                    }
                }
            }
        }

        $client = new PreferenceClient();

        $tenantId = auth()->user()->tenant_id ?? 1;

        $preference = $client->create([
            "items" => array(
                array(
                    "id" => (string) $plan->id,
                    "title" => 'Suscripción: ' . $plan->name,
                    "quantity" => 1,
                    "unit_price" => $unitPrice,
                    "currency_id" => $plan->currency ?? 'MXN',
                )
            ),
            "back_urls" => array(
                "success" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=success",
                "failure" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=failure",
                "pending" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=pending"
            ),
            // "auto_return" => "approved", // Desactivado porque MercadoPago rechaza localhost con auto_return
            "external_reference" => $tenantId . '|' . $plan->id . '|' . $promoCodeIdToSave
        ]);

        return response()->json([
            'preference_id' => $preference->id,
            'init_point' => env('APP_ENV') === 'production' ? $preference->init_point : $preference->sandbox_init_point
        ]);
    }

    public function webhook(Request $request)
    {
        // 1. Obtener el ID del pago
        $topic = $request->input('type') ?? $request->input('topic');
        $id = $request->input('data.id') ?? $request->input('id');

        // 2. Validar firma del webhook (Seguridad adicional)
        $signatureHeader = $request->header('x-signature');
        $webhookSecret = env('MERCADOPAGO_WEBHOOK_SECRET');

        if ($signatureHeader && $webhookSecret && $id) {
            // El header viene en formato: ts=1710000000,v1=abcdef123456...
            $parts = explode(',', $signatureHeader);
            $ts = '';
            $v1 = '';

            foreach ($parts as $part) {
                if (strpos($part, 'ts=') === 0) $ts = substr($part, 3);
                if (strpos($part, 'v1=') === 0) $v1 = substr($part, 3);
            }

            // Calculamos el hash esperado
            $manifest = "id:$id;request-id:{$request->header('x-request-id')};ts:$ts;";
            $expectedSignature = hash_hmac('sha256', $manifest, $webhookSecret);

            if (!hash_equals($expectedSignature, $v1)) {
                \Illuminate\Support\Facades\Log::warning('MercadoPago Webhook Signature mismatch.');
                // return response()->json(['status' => 'unauthorized'], 401); // Opcional: Rechazar la petición
            }
        }

        // 3. Procesar el pago
        if ($topic === 'payment' && $id) {
            try {
                $client = new PaymentClient();
                $payment = $client->get($id);
                
                if ($payment && $payment->status === 'approved') {
                    $externalReference = $payment->external_reference;
                    if ($externalReference && strpos($externalReference, '|') !== false) {
                        $parts = explode('|', $externalReference);
                        $tenantId = $parts[0] ?? null;
                        $planId = $parts[1] ?? null;
                        $promoCodeId = $parts[2] ?? null;
                        
                        $tenant = \App\Models\Tenant::find($tenantId);
                        if ($tenant) {
                            $subscription = \App\Models\Subscription::firstOrNew(['tenant_id' => $tenantId]);
                            $subscription->plan_id = $planId;
                            $subscription->starts_at = now();
                            $subscription->ends_at = now()->addMonth();
                            $subscription->status = 'active';
                            
                            if ($promoCodeId) {
                                $subscription->promo_code_id = $promoCodeId;
                                $promoCode = PromoCode::find($promoCodeId);
                                if ($promoCode) {
                                    $promoCode->increment('uses_count');
                                }
                            }
                            
                            $subscription->save();
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing MercadoPago webhook: ' . $e->getMessage());
                // Devolvemos 200 para que MercadoPago no siga reintentando notificaciones fallidas o de prueba
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
            }
        }

        return response()->json(['status' => 'received'], 200);
    }
}
