<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          $facilities = [
            [
                'name' => 'Health Insurance',
                'description' => 'Provides access to basic health coverage and medical benefits.',
                'is_active' => true,
            ],
            [
                'name' => 'Pension Scheme',
                'description' => 'Monthly pension for retired or unorganised sector workers.',
                'is_active' => true,
            ],
            [
                'name' => 'Skill Development',
                'description' => 'Training programs to enhance skills for employment opportunities.',
                'is_active' => true,
            ],
            [
                'name' => 'Housing Assistance',
                'description' => 'Support for affordable housing under government schemes.',
                'is_active' => true,
            ],
            [
                'name' => 'Financial Aid',
                'description' => 'Direct financial support for workers in need.',
                'is_active' => true,
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}
