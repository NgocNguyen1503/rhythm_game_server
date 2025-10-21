<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HighScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            DB::table('high_scores')->insert([
                'user_id' => 2,
                'song_id' => 1,
                'difficulty' => $i,
                'high_score' => fake()->numberBetween(1, 100),
                'full_combo' => fake()->numberBetween(0, 1),
                'all_perfect' => fake()->numberBetween(0, 1),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
