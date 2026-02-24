<?php

namespace Database\Seeders;

use App\Models\SchemesPage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchemesPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $schemes = [
            [
                'name' => "Pradhan Mantri Shram Yogi Maandhan (PMSYM)",
                'description' => "Pension scheme for unorganised sector workers.",
                'url' => "https://labour.gov.in/pmsym",
            ],
            [
                'name' => "eShram",
                'description' => "National database for unorganised workers linked to welfare schemes.",
                'url' => "https://eshram.gov.in/socialsecuritywelfareschemes",
            ],
            [
                'name' => "Pradhan Mantri Jeevan Jyoti Bima Yojana (PMJJBY)",
                'description' => "Life insurance cover for eligible account holders.",
                'url' => "https://www.pmjjby.gov.in/",
            ],
            [
                'name' => "Pradhan Mantri Suraksha Bima Yojana (PMSBY)",
                'description' => "Accident and death insurance for bank account holders.",
                'url' => "https://pmsby.gov.in/",
            ],
            [
                'name' => "Pradhan Mantri Kaushal Vikas Yojana (PMKVY)",
                'description' => "Skill training and certification for youth and workers.",
                'url' => "https://www.msde.gov.in/offerings/schemesandservices/details/pradhanmantrikaushalvikasyojana40pmkvy402021ITO3ATMtQWa",
            ],
            [
                'name' => "National Career Service (NCS)",
                'description' => "Job and skill matching portal for workers.",
                'url' => "https://www.ncs.gov.in/",
            ],
            [
                'name' => "Pradhan Mantri Awas Yojana (PMAYU)",
                'description' => "Housing for all mission by Government of India.",
                'url' => "https://pmaymis.gov.in/",
            ],
        ];

        foreach ($schemes as $scheme) {
            SchemesPage::create($scheme);
        }
    }
}
