<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use DB;

class EmployerSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            // Insert employer
            $employerId = DB::table('employers')->insertGetId([
                'email' => "employer{$i}@example.com",
                'phone' => str_pad((string) $i, 10, '9', STR_PAD_LEFT),
                'password' => Hash::make('password1230'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert employer profile linked to employer_id
            DB::table('employer_profiles')->insert([
                'employer_id' => $employerId,
                'emp_type' => 'Contractor',
                'name' => "Employer {$i} Pvt Ltd",
                'avg_worker' => (string) rand(10, 200),
                'work_type' => 'Construction',
                'location' => "City {$i}",
                'availability' => 'Yes',
                'eshram' => null,
                'aadhar' => null,
                'docType' => 'PAN',
                'docNumber' => "ABCDE{$i}1234F",
                'pdf' => null,
                'bocw' => null,
                'language' => 'Hindi',
                'created_at' => now(),
                'updated_at' => now(),
                'plot_no' => "Plot {$i}",
                'street_area_village' => "Street {$i}",
                'post_office' => "PO {$i}",
                'district' => "District {$i}",
                'state' => "State {$i}",
                'pin_code' => "1100{$i}",
                'profile_image' => null,
                'gender' => 'male',
            ]);
        }
    }
}