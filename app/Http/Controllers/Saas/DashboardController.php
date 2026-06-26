<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales básicos
        $totalTenants = Tenant::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        // Ingresos del mes actual
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Asumiendo que cada registro en subscriptions que empieza este mes equivale a un cobro:
        $monthlyRevenue = Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                            ->where('subscriptions.starts_at', '>=', $startOfMonth)
                            ->sum('plans.price');

        // Distribución de planes
        $plansDistribution = Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->select('plans.name', DB::raw('count(*) as count'), 'plans.price')
            ->groupBy('plans.name', 'plans.price')
            ->get();

        // Actividad Reciente (últimas 5 clínicas registradas)
        $recentTenants = Tenant::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'total_tenants' => $totalTenants,
            'active_subscriptions' => $activeSubscriptions,
            'monthly_revenue' => $monthlyRevenue,
            'plans_distribution' => $plansDistribution,
            'recent_tenants' => $recentTenants
        ]);
    }
}
