<?php

namespace Database\Seeders;

use App\Models\TimeEntries;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        /* TimeEntries::factory(30)->create(); */

        Role::create([
            'name' => 'admin',
        ]);

        Role::create([
            'name'=> 'user'
        ]);

        User::factory()->create([
            'name' => 'Raúl Henares Palacios',
            'email' => 'raul@gmail.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Javi',
            'email' => 'javier.ruiz@doc.medac.es',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        User::factory()->create([
            "name"=>"Raúl Henares Palacios",
            "email"=>"rauluser@gmail.com",
            "password"=>Hash::make("password"),
        ])->assignRole("user");
    }
}
