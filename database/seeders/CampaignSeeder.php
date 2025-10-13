<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\User;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $receivers = User::where('role', 'receiver')->get();

        foreach ($receivers as $receiver) {
            Campaign::create([
                'creator_id' => $receiver->id,
                'title' => 'Winter Food Drive',
                'description' => 'Collecting food donations for families in need during winter season',
                'goal_description' => 'Feed 100 families throughout the winter',
                'target_items' => 'Canned goods, dry foods, fresh produce',
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'status' => 'active',
            ]);
        }

        // Add one more campaign
        Campaign::create([
            'creator_id' => $receivers->first()->id,
            'title' => 'Back to School Nutrition Program',
            'description' => 'Providing healthy meals for underprivileged children going back to school',
            'goal_description' => 'Support 50 children with daily nutritious meals',
            'target_items' => 'Breakfast items, lunch boxes, fruits, milk',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addMonths(4),
            'status' => 'draft',
        ]);
    }
}
