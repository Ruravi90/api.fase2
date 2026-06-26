<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Traemos todas las suscripciones de todos los tenants con sus relaciones
        $subscriptions = Subscription::with(['tenant', 'plan'])->orderBy('created_at', 'desc')->get();
        return response()->json($subscriptions);
    }
}
