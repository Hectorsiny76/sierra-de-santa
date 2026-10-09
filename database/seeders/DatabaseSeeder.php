<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\RoomImage;
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

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);



    }
}
