<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ConfigSeeder;
use Database\Seeders\WorkerSeeder;
use Database\Seeders\SchemesPagesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);


        // ✅ Register your seeders here
        $this->call([
           ConfigSeeder::class,
           SchemesPagesSeeder::class,
           FacilitiesSeeder::class,
           SkillSeeder::class,
           EmployerSeeder::class, // <-- add this line
        //    WorkerSeeder::class
        ]);

    }
}
