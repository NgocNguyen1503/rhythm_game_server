<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Bill;
use App\Models\HighScore;
use App\Models\Song;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function listSong(Request $request)
    {
        $params = $request->all();
        $user = User::find(Auth::id());

        $songs = Song::select(
            'id',
            'name',
            'thumbnail',
            'price',
            'mp3_url',
            'beatmap_easy',
            'beatmap_normal',
            'beatmap_hard',
        )->get();

        if ($user->role == 2) {
            $bills = Bill::where('user_id', Auth::id())->get();

            foreach ($songs as $song) {
                if ($song->price == 0) {
                    $song->status = 'unlocked';
                }
                if ($song->price != 0) {
                    $song->status = 'locked';
                    foreach ($bills as $bill) {
                        if ($bill->song_id == $song->id) {
                            $song->status = 'unlocked';
                        }
                    }
                }

                $song->high_score = HighScore::select('difficulty', 'high_score', 'full_combo', 'all_perfect')
                    ->where('user_id', Auth::id())
                    ->where('song_id', $song->id)
                    ->get();
            }
        }

        return ResponseApi::success($songs);
    }

    public function updateHighScore(Request $request)
    {
        $params = $request->all();
        HighScore::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'song_id' => $params['song_id'],
                'difficulty' => $params['difficulty'],
            ],
            [
                'high_score' => $params['high_score'],
                'full_combo' => $params['full_combo'],
                'all_perfect' => $params['all_perfect'],
                'updated_at' => now(),
            ]
        );
        return ResponseApi::success();
    }
}