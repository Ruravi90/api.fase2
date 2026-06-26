<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = \Spatie\Multitenancy\Models\Tenant::current();

        if (!$tenant) {
            return response()->json(['message' => 'No tenant context found'], 403);
        }

        $subscription = \App\Models\Subscription::where('tenant_id', $tenant->id)
            ->whereIn('status', ['active', 'trial'])
            ->where(function ($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription expired or inactive. Please renew your plan.'], 403);
        }

        return $next($request);
    }
}
