<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');

        DB::table('users')->insert([
            'name' => 'Wildan Athif Muttaqien',
            'email' => 'muttaqien.wildan12@gmail.com',
            'password' => Hash::make('12345678'), // Gunakan Hash::make() untuk mengenkripsi password
            'remember_token' => 'bxdEbV57NykAoDuAzwppTceuluMxprwARX1cfFzm9C6liQrcASGzhiP7RI4Y',
            'created_at' => '2025-05-07 07:38:05',
            'updated_at' => '2025-05-07 07:38:05',
        ]);
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}
