<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         DB::table('configs')->insert([
            [
                'key'   => 'help_phone',
                'value' => '011 23473276',
            ],
            [
                'key'   => 'help_email',
                'value' => 'bocwhelpdesk@gov.in',
            ],
             [
                'key'   => 'worker_agreement',
                'value' => "I hereby give my consent to Digital Labour Chowk to collect, store, process, and share my personal information, including but not limited to my name, mobile number, email address, location, skills, work experience, education details, resume/CV, documents, and profile photo, with registered employers on this platform for the purpose of job matching and recruitment.

I understand that:

My information will be shared only with verified employers seeking candidates for relevant job roles.

I can update or delete my information at any time through my account settings.

Digital Labour Chowk will not sell, rent, or misuse my personal data for any unauthorized purpose.

I can withdraw my consent at any time, though this may limit my ability to receive job opportunities and platform services.

By proceeding, I confirm that I have read and agree to the Privacy Policy and Terms of Use of Digital Labour Chowk.",
            ],
            [
                'key'   => 'employer_agreement',
                'value' => "I hereby give my consent to Digital Labour Chowk to collect, store, and verify the information I provide, including company details, authorized person’s name, contact number, email address, job requirements, and any necessary documents required for recruitment purposes.

I understand and agree that:

I will be granted access to view workers’ personal information (name, phone number, email, skills, experience, education, etc.) strictly for recruitment purposes only.

I shall not share, sell, disclose, or misuse any worker data obtained from the platform to any unauthorized third party.

I will use the worker data only for genuine hiring purposes as per the platform’s rules and policies.

Any misuse of worker information may result in suspension of my account, permanent ban, and possible legal action.",
            ],
        ]);
    }
}
