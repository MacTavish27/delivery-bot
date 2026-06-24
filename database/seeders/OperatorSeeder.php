<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::create([
            'name'      => 'Test Do\'kon',
            'slug'      => 'test-dokon',
            'phone'     => '+998901234567',
            'address'   => 'Toshkent',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Operator',
            'email'     => 'operator@delivery.uz',
            'password'  => bcrypt('password'),
            'role'      => 'operator',
            'tenant_id' => $tenant->id,
        ]);
    }
}
