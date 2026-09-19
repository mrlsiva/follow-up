<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\{LeadSource, Service};
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
        $sources = ['Justdial', 'Meta Ads', 'Website', 'WhatsApp', 'Referral', 'Walk-in', 'IndiaMART', 'Google Ads', 'Other'];
        foreach ($sources as $index => $name) LeadSource::create(['name' => $name, 'color' => ['#F4B942','#4DA3A7','#E9775B','#78A6D8'][$index % 4]]);
        foreach (['Website', 'Mobile App', 'SEO', 'Digital Marketing', 'Billing Software', 'Custom Software'] as $name) Service::create(['name' => $name]);
        User::create(['name' => 'Aarav Mehta', 'email' => 'admin@example.com', 'password' => 'password']);
    }
}
