<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the primary tenant
        $tenant = \App\Models\Tenant::firstOrCreate(
            ['id' => 1],
            ['name' => 'Fase2Spa', 'domain' => 'fase2spa.local']
        );

        // 2. Create all available system features (modules)
        $featSales = \App\Models\Feature::firstOrCreate(['code' => 'module-sales'], ['name' => 'Módulo de Ventas']);
        $featInventory = \App\Models\Feature::firstOrCreate(['code' => 'module-inventory'], ['name' => 'Módulo de Inventario']);
        $featClients = \App\Models\Feature::firstOrCreate(['code' => 'module-clients'], ['name' => 'Módulo de Pacientes/Clientes']);
        $featProviders = \App\Models\Feature::firstOrCreate(['code' => 'module-providers'], ['name' => 'Módulo de Proveedores']);
        $featAgenda = \App\Models\Feature::firstOrCreate(['code' => 'module-agenda'], ['name' => 'Módulo de Agenda']);
        $featPackages = \App\Models\Feature::firstOrCreate(['code' => 'module-packages'], ['name' => 'Módulo de Paquetes/Tratamientos']);
        $featClinicalNotes = \App\Models\Feature::firstOrCreate(['code' => 'module-clinical-notes'], ['name' => 'Notas Clínicas']);
        $featMedicalRecords = \App\Models\Feature::firstOrCreate(['code' => 'module-medical-records'], ['name' => 'Expediente Médico']);
        $featBox = \App\Models\Feature::firstOrCreate(['code' => 'module-box'], ['name' => 'Corte de Caja (Finanzas)']);
        $featPurchases = \App\Models\Feature::firstOrCreate(['code' => 'module-purchases'], ['name' => 'Módulo de Compras']);
        $featCatalogs = \App\Models\Feature::firstOrCreate(['code' => 'module-catalogs'], ['name' => 'Catálogos Generales']);
        $featWhatsapp = \App\Models\Feature::firstOrCreate(['code' => 'module-whatsapp'], ['name' => 'Integración WhatsApp']);
        $featInternalChat = \App\Models\Feature::firstOrCreate(['code' => 'module-internal-chat'], ['name' => 'Chat Interno']);
        $featLimitUsers = \App\Models\Feature::firstOrCreate(['code' => 'limit-users'], ['name' => 'Límite de Usuarios']);

        // 3. Create Plans
        // Plan Mezquite (Básico)
        $planMezquite = \App\Models\Plan::firstOrCreate(
            ['name' => 'Plan Mezquite'],
            ['description' => 'Básico para emprendedores', 'price' => 299, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        $planMezquite->features()->sync([
            $featAgenda->id,
            $featClients->id,
            $featClinicalNotes->id,
            $featMedicalRecords->id,
            $featCatalogs->id,
            $featLimitUsers->id => ['limit_value' => 1]
        ]);

        // Plan Ceiba (Intermedio)
        $planCeiba = \App\Models\Plan::firstOrCreate(
            ['name' => 'Plan Ceiba'],
            ['description' => 'Para clínicas en crecimiento', 'price' => 599, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        $planCeiba->features()->sync([
            $featAgenda->id,
            $featClients->id,
            $featClinicalNotes->id,
            $featMedicalRecords->id,
            $featCatalogs->id,
            $featSales->id,
            $featInventory->id,
            $featPackages->id,
            $featBox->id,
            $featInternalChat->id,
            $featLimitUsers->id => ['limit_value' => 5]
        ]);

        // Plan Ahuehuete (Avanzado)
        $planAhuehuete = \App\Models\Plan::firstOrCreate(
            ['name' => 'Plan Ahuehuete'],
            ['description' => 'Automatización y escala', 'price' => 999, 'billing_cycle' => 'monthly', 'is_active' => true]
        );
        $planAhuehuete->features()->sync([
            $featAgenda->id,
            $featClients->id,
            $featClinicalNotes->id,
            $featMedicalRecords->id,
            $featCatalogs->id,
            $featSales->id,
            $featInventory->id,
            $featPackages->id,
            $featBox->id,
            $featInternalChat->id,
            $featProviders->id,
            $featPurchases->id,
            $featWhatsapp->id,
            $featLimitUsers->id => ['limit_value' => null] // null means unlimited
        ]);

        // 4. Create Subscription for Fase2Spa
        \App\Models\Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $planAhuehuete->id,
                'starts_at' => now(),
                'ends_at' => now()->addYears(10), // Lifetime/Long term for the owner
                'status' => 'active'
            ]
        );
    }
}
