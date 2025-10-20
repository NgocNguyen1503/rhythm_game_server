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

    private $saveName = [
        'Twinkle_Twinkle_Little_Star',
        'Deemo_Pure_White',
        'Mili_Utopiosphere',
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
            $saveNamePath = $this->saveName[$i];
            $basePath = $publicPath . $saveNamePath . '/';

            DB::table('songs')->insert([
                'name' => $name,
                'thumbnail' => $basePath . $saveNamePath . '_thumbnail.png',
                'mp3_url' => $basePath . $saveNamePath . '.mp3',
                'beatmap_easy' => $basePath . 'beatmaps/' . $saveNamePath . '_easy.json',
                'beatmap_normal' => $basePath . 'beatmaps/' . $saveNamePath . '_normal.json',
                'beatmap_hard' => $basePath . 'beatmaps/' . $saveNamePath . '_hard.json',
                'price' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
