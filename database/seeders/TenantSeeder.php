<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the primary tenant
        $tenant = \Spatie\Multitenancy\Models\Tenant::firstOrCreate(
            ['id' => 1],
            ['name' => 'Fase2Spa', 'domain' => 'fase2spa.local']
        );

        // 2. Create basic features
        $featureSales = \App\Models\Feature::firstOrCreate(['code' => 'module-sales'], ['name' => 'Módulo de Ventas']);
        $featureInventory = \App\Models\Feature::firstOrCreate(['code' => 'module-inventory'], ['name' => 'Módulo de Inventario']);

        // 3. Create Plans
        $planTrial = \App\Models\Plan::firstOrCreate(
            ['name' => 'Prueba 1 Mes'],
            ['description' => 'Plan básico de prueba', 'price' => 0, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        $planTrial->features()->syncWithoutDetaching([$featureSales->id, $featureInventory->id]);

        $planPremium = \App\Models\Plan::firstOrCreate(
            ['name' => 'Plan Premium'],
            ['description' => 'Acceso completo al sistema', 'price' => 500, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        $planPremium->features()->syncWithoutDetaching([$featureSales->id, $featureInventory->id]);

        // 4. Create Subscription for Fase2Spa
        \App\Models\Subscription::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $planPremium->id,
                'starts_at' => now(),
                'ends_at' => now()->addYears(10), // Lifetime/Long term for the owner
                'status' => 'active'
            ]
        );
    }
}
