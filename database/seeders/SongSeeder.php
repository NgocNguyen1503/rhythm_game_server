<?php

namespace Database\Seeders;

use App\Models\Song;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SongSeeder extends Seeder
{
    private $names = [
        'Twinkle Twinkle Little Star',
        'Deemo - Pure White',
        'Mili - Utopiosphere',
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $from = count(Song::all());
        for ($i = $from; $i < $from + 3; $i++) {
            DB::table('songs')->insert([
                'name' => $this->names[$i],
                'thumbnail' => $this->names[$i] . '_thumbnail.png',
                'beatmap_easy' => 'beatmaps/' . $this->names[$i] . '_easy.json',
                'beatmap_normal' => 'beatmaps/' . $this->names[$i] . '_normal.json',
                'beatmap_hard' => 'beatmaps/' . $this->names[$i] . '_hard.json',
                'price' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
