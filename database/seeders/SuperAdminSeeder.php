<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@delivery.uz',
            'password' => bcrypt('123456'),
            'role'     => 'superadmin',
        ]);
    }
}
