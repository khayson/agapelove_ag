<?php

namespace Database\Seeders;

use App\Models\ChurchMember;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pastor',
            'email' => 'pastor@agapelove.com',
            'password' => Hash::make('123password'),
        ]);

        ChurchMember::factory(400)->create();
    }
}
