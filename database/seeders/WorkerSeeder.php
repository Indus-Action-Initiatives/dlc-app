<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Worker;
use App\Models\WorkerProfile;

class WorkerSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            // Generate a 10-digit phone number (e.g., 8000000001 → 8000000050)
            $phone = str_pad((string)$i, 10, '8', STR_PAD_LEFT);

            // Create worker
            $worker = Worker::create([
                'email' => "worker{$i}@example.com",
                'phone' => $phone,
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create worker profile linked to worker_id
            WorkerProfile::create([
                'worker_id' => $worker->id,
                'name' => "Worker {$i}",
                'rate' => (string) rand(300, 1500),
                'age' => rand(18, 55),
                'skill_id' => json_encode([
                    rand(1, 10),
                    rand(11, 20),
                ]),
                'experience' => (string) rand(1, 10),
                'work_type' => fake()->randomElement(['Daily Wage', 'Monthly']),
                'location' => "City {$i}",
                'availability' => fake()->randomElement(['Yes', 'No']),
                'eshram' => null,
                'aadhar' => null,
                'docType' => 'PAN',
                'docNumber' => "ABCDE{$i}1234F",
                'pdf' => null,
                'language' => fake()->randomElement(['Hindi', 'English']),
                'created_at' => now(),
                'updated_at' => now(),
                'plot_no' => "Plot {$i}",
                'street_area_village' => "Street {$i}",
                'post_office' => "PO {$i}",
                'district' => "District {$i}",
                'state' => "State {$i}",
                'pin_code' => "1100{$i}",
                'profile_image' => null,
                'gender' => fake()->randomElement(['male', 'female']),
                'lat' => (string) fake()->latitude(20.0, 30.0),
                'long' => (string) fake()->longitude(70.0, 90.0),
            ]);
        }
    }
}
