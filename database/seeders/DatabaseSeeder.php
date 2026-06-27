<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\CatExpense;
use App\Models\CatTypeSale;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'module_rol',
            'add_rol',
            'edit_rol',
            'delete_rol',
            'module_user',
            'add_user',
            'edit_user',
            'delete_user',
            'module_client',
            'add_client',
            'edit_client',
            'delete_client',
            'module_provider',
            'add_provider',
            'edit_provider',
            'delete_provider',
            'module_creditor',
            'add_creditor',
            'edit_creditor',
            'delete_creditor',
            'module_product_inventory',
            'add_product_inventory',
            'edit_product_inventory',
            'delete_product_inventory',
            'module_pill_inventory',
            'add_pill_inventory',
            'edit_pill_inventory',
            'delete_pill_inventory',
            'module_cat_reference',
            'add_cat_reference',
            'edit_cat_reference',
            'delete_cat_reference',
            'module_cat_package',
            'add_cat_package',
            'edit_cat_package',
            'delete_cat_package',
            'module_cat_product',
            'add_cat_product',
            'edit_cat_product',
            'delete_cat_product',
            'module_cat_pill',
            'add_cat_pill',
            'edit_cat_pill',
            'delete_cat_pill',
            'module_sale',
            'add_sale',
            'edit_sale',
            'delete_sale',
            'module_schedule',
            'add_schedule',
            'edit_schedule',
            'delete_schedule',
            'module_package',
            'module_purchases',
            'module_box',
            'module_clinical_note',
            'add_clinical_note',
            'edit_clinical_note',
            'delete_clinical_note',
            'module_medical_record',
            'add_medical_record',
            'edit_medical_record',
            'delete_medical_record',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission],
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ]
            );
        }

        $roles = [
            'super_admin',
            'admin',
            'user',
            'agent',
            'medico',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['slug' => $roleName],
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]
            );
        }

        $allPermissions = Permission::all();
        Role::where('slug', 'super_admin')->first()->syncPermissions($allPermissions);
        Role::where('slug', 'admin')->first()->syncPermissions($allPermissions);

        $medicoPermissionsSlugs = [
            'module_clinical_note',
            'add_clinical_note',
            'edit_clinical_note',
            'delete_clinical_note',
            'module_medical_record',
            'add_medical_record',
            'edit_medical_record',
            'delete_medical_record',
            'module_client', // Permiso para ver la lista de pacientes/clientes
            'module_schedule', // Permiso para ver la agenda
        ];
        
        $medicoPermissions = Permission::whereIn('slug', $medicoPermissionsSlugs)->get();
        Role::where('slug', 'medico')->first()->syncPermissions($medicoPermissions);

        $adminRole = Role::where('slug', 'admin')->first();
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $agentRole = Role::where('slug', 'agent')->first();

        User::updateOrCreate(
            ['username' => 'raguilar'],
            [
                'name' => 'Ruravi',
                'lastname' => 'Aguilar',
                'motherlastname' => 'Arrezola',
                'email' => 'ruravi.app@gmail.com',
                'password' => Hash::make('Ruravi90#'),
            ]
        )->syncRoles([$adminRole, $superAdminRole]);

        CatExpense::firstOrCreate(['name' => 'Pastillas']);
        CatExpense::firstOrCreate(['name' => 'Productos']);

        CatTypeSale::firstOrCreate(['name' => 'Efectivo']);
        CatTypeSale::firstOrCreate(['name' => 'Debito']);
        CatTypeSale::firstOrCreate(['name' => 'Credito']);
    }
}
