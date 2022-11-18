<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CatExpense;
use App\Models\CatTypeSale;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['username' => 'raguilar'],
            ['name' => 'Ruravi','lastname' => 'Aguilar','motherlastname' => 'Arrezola','email' => 'ruravi.app@gmail.com','password' => bcrypt('ruravi90')]
        );

        User::updateOrCreate(
                ['username' => 'jcuevas'],
                ['name' => 'Joel','lastname' => 'Cuevas','motherlastname' => '','email' => 'jcuqevas@Appspa.com.mx','password' => bcrypt('jcuevas')]
        );

        User::updateOrCreate(
            ['username' => 'agente1'],
            ['name' => 'Agente','lastname' => 'prueba','email' => 'agente@Appspa.com.mx','password' => bcrypt('agente1')]
        );

        CatExpense::updateOrCreate(
                ['name' => 'Pastillas'],
                ['name' => 'Pastillas']
        );

        CatExpense::updateOrCreate(
                ['name' => 'Productos'],
                ['name' => 'Productos']
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
    }
}
