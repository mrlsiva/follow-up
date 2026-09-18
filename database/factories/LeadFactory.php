<?php

namespace Database\Factories;

use App\Models\{Lead, LeadSource, Service};
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;
    public function definition(): array
    {
        return ['full_name' => fake()->name(), 'mobile_number' => fake()->unique()->numerify('98########'), 'email' => fake()->safeEmail(), 'company_name' => fake()->company(), 'city' => fake()->city(), 'state' => fake()->randomElement(['Maharashtra', 'Karnataka', 'Delhi', 'Gujarat']), 'pincode' => fake()->numerify('######'), 'lead_source_id' => LeadSource::inRandomOrder()->value('id'), 'service_id' => Service::inRandomOrder()->value('id'), 'status' => fake()->randomElement(['New', 'Contacted', 'Interested', 'Follow-up', 'Converted', 'Lost']), 'priority' => fake()->randomElement(['High', 'Medium', 'Low']), 'next_followup_at' => fake()->optional(0.75)->dateTimeBetween('-2 days', '+10 days'), 'remarks' => fake()->optional()->sentence()];
    }
}
