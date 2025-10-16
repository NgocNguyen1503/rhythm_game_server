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
        $publicPath = 'http://localhost:8000/songs/';
        $from = Song::count();

        for ($i = $from; $i < $from + 3; $i++) {
            $name = $this->names[$i];
            $basePath = $publicPath . $name . '/';

            DB::table('songs')->insert([
                'name' => $name,
                'thumbnail' => $basePath . $name . '_thumbnail.png',
                'mp3_url' => $basePath . $name . '.mp3',
                'beatmap_easy' => $basePath . 'beatmaps/' . $name . '_easy.json',
                'beatmap_normal' => $basePath . 'beatmaps/' . $name . '_normal.json',
                'beatmap_hard' => $basePath . 'beatmaps/' . $name . '_hard.json',
                'price' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
