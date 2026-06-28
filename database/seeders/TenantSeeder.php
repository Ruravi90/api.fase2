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

        // 2. Create all available system features (modules)
        $features = [
            \App\Models\Feature::firstOrCreate(['code' => 'module-sales'], ['name' => 'Módulo de Ventas']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-inventory'], ['name' => 'Módulo de Inventario']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-clients'], ['name' => 'Módulo de Pacientes/Clientes']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-providers'], ['name' => 'Módulo de Proveedores']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-agenda'], ['name' => 'Módulo de Agenda']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-packages'], ['name' => 'Módulo de Paquetes/Tratamientos']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-clinical-notes'], ['name' => 'Notas Clínicas']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-medical-records'], ['name' => 'Expediente Médico']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-box'], ['name' => 'Corte de Caja (Finanzas)']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-purchases'], ['name' => 'Módulo de Compras']),
            \App\Models\Feature::firstOrCreate(['code' => 'module-catalogs'], ['name' => 'Catálogos Generales'])
        ];

        // 3. Create Plans
        $planTrial = \App\Models\Plan::firstOrCreate(
            ['name' => 'Prueba 1 Mes'],
            ['description' => 'Plan básico de prueba', 'price' => 0, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        // Sync just a few features for the trial plan
        $planTrial->features()->syncWithoutDetaching([$features[0]->id, $features[1]->id, $features[2]->id, $features[4]->id]);

        $planPremium = \App\Models\Plan::firstOrCreate(
            ['name' => 'Plan Premium'],
            ['description' => 'Acceso completo al sistema', 'price' => 500, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        // Sync all features for the premium plan
        $planPremium->features()->syncWithoutDetaching(collect($features)->pluck('id')->toArray());

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
