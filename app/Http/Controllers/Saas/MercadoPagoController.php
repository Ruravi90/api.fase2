<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;

class MercadoPagoController extends Controller
{
    public function __construct()
    {
        // En un entorno real, esto vendría de config('services.mercadopago.token')
        SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-000000000000-xxxx-xxxx'));
    }

    public function createPreference(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $preference = new Preference();

        $item = new Item();
        $item->title = 'Suscripción: ' . $plan->name;
        $item->quantity = 1;
        $item->unit_price = (float) $plan->price;
        $item->currency_id = $plan->currency;

        $preference->items = array($item);
        
        // URLs de retorno
        $preference->back_urls = array(
            "success" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=success",
            "failure" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=failure",
            "pending" => env('APP_FRONTEND_URL', 'http://localhost:4200') . "/#/subscription?status=pending"
        );
        $preference->auto_return = "approved";

        // Aquí podríamos guardar el ID del tenant para saber quién está pagando
        $tenantId = auth()->user()->tenant_id ?? 1;
        $preference->external_reference = $tenantId . '|' . $plan->id;

        $preference->save();

        return response()->json([
            'preference_id' => $preference->id,
            'init_point' => env('APP_ENV') === 'production' ? $preference->init_point : $preference->sandbox_init_point
        ]);
    }

    public function webhook(Request $request)
    {
        return response()->json(['status' => 'received']);
    }
}
