<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\CatReference;
use App\Models\CatExpense;
use App\Models\CatProduct;
use App\Models\CatService;
use App\Models\CatTypeSale;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        CatExpense::updateOrCreate(
            ['name' => 'Pastillas'],
            ['name' => 'Pastillas']
        );
        CatExpense::updateOrCreate(
            ['name' => 'Productos'],
            ['name' => 'Productos']
        );

        CatReference::updateOrCreate(
            ['name' => 'Redes Sociales'],
            ['name' => 'Redes Sociales']
        );
        CatReference::updateOrCreate(
            ['name' => 'Lona'],
            ['name' => 'Lona']
        );

        CatTypeSale::updateOrCreate(
            ['name' => 'Efectivo'],
            ['name' => 'Efectivo']
        );
        CatTypeSale::updateOrCreate(
            ['name' => 'Debito'],
            ['name' => 'Debito']
        );
        CatTypeSale::updateOrCreate(
            ['name' => 'Credito'],
            ['name' => 'Credito']
        );

        CatProduct::updateOrCreate(
            ['name' => 'Gel reductivo'],
            ['name' => 'Gel reductivo', 'price'=>500]
        );
        CatProduct::updateOrCreate(
            ['name' => 'Faja'],
            ['name' => 'Faja', 'price'=>500]
        );
        CatProduct::updateOrCreate(
            ['name' => 'Otro'],
            ['name' => 'Otro', 'price'=>0]
        );

        CatService::updateOrCreate(
            ['name' => 'Masaje'],
            ['name' => 'Masaje', 'price'=>500]
        );
        CatService::updateOrCreate(
            ['name' => 'Depilacion'],
            ['name' => 'Depilacion', 'price'=>500]
        );
        CatService::updateOrCreate(
            ['name' => 'Otro'],
            ['name' => 'Otro', 'price'=>0]
        );

        Department::updateOrCreate(
            ['name' => 'Fase 2'],
        );
        Department::updateOrCreate(
            ['name' => 'Otro'],
        );

        User::updateOrCreate(
            ['username' => 'raguilar'],
            ['initials' => 'raa','name' => 'Ruravi','lastname' => 'Aguilar','motherlastname' => 'Arrezola','email' => 'ruravi.app@gmail.com','password' => bcrypt('ruravi90'),'profile'=>'admin']
        );

        User::updateOrCreate(
            ['username' => 'jcuevas'],
            ['initials' => 'jc','name' => 'Joel','lastname' => 'Cuevas','motherlastname' => '','email' => 'jcuqevas@Appspa.com.mx','password' => bcrypt('jcuevas'),'profile'=>'admin']
        );

        User::updateOrCreate(
            ['username' => 'agente1'],
            ['initials' => 'age','name' => 'Agente','lastname' => 'prueba','email' => 'agente@Appspa.com.mx','password' => bcrypt('agente1'),'profile'=>'agent']
        );

        Client::updateOrCreate(
            ['name' => 'Ernesto'],
            ['name' => 'Ernesto','lastname' => 'Padilla','reference_id' => CatReference::inRandomOrder()->first()->id]
        );

        Client::updateOrCreate(
            ['name' => 'Josue'],
            ['name' => 'Josue','lastname' => 'Santana','reference_id' => CatReference::inRandomOrder()->first()->id]
        );

        Client::updateOrCreate(
            ['name' => 'Nohemi'],
            ['name' => 'Nohemi','lastname' => 'Renteria','reference_id' => CatReference::inRandomOrder()->first()->id]
        );

    }
}
