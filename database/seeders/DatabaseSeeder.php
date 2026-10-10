<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\RoomImage;
use App\Models\User;
use App\UserRole;
use Database\Factories\RoomImageFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Payment::factory(10)->create();
        RoomImage::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admintest@test.com',
            'phone' => '0123456789',
            'password' => bcrypt('adminpassword08'),
            'role' => UserRole::Admin,
        ]);



    }
}
