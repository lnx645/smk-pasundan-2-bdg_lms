<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@gmail.com')->count() <= 0) {
            User::factory()->createOne([
                'name' => "admin",
                'email' => 'admin@gmail.com',
                'is_admin' => true
            ]);
        }
        // $this->call([
        //     MatpelSeeder::class,
        //     KelasSeeder::class,
        //     SiswaSeeder::class,
        //     GuruSeeder::class,
        //     MateriSeeder::class,
        // ]);
    }
}
