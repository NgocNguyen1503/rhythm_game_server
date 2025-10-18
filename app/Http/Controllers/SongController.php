<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\Bill;
use App\Models\HighScore;
use App\Models\Song;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function addNewSong(Request $request)
    {
        $params = $request->all();
        $request->validate([
            'input' => 'required|string',
            'name' => 'required|string',
            'audio' => 'required|string',
        ]);

        $input = $request->input('input');
        $name = $request->input('name');
        $audio = $request->input('audio');

        $publicPath = 'http://localhost:8000/songs/';
        $basePath = $publicPath . $name . '/';

        try {
            // Call Flask API
            $response = Http::timeout(600)
                ->withoutVerifying()
                ->post('http://127.0.0.1:5000/generate', [
                    'input' => $input,
                    'name' => $name,
                    'audio' => $audio,
                ]);

            if (!$response->successful()) {
                Log::error('Flask API error: ' . $response->body());
                return response()->json([
                    'error' => 'Flask API không phản hồi hoặc lỗi nội bộ',
                    'details' => $response->body(),
                ], 500);
            }

            $data = $response->json();

            if (empty($data['title']) || empty($data['beatmaps'])) {
                Log::error('Flask không trả về dữ liệu hợp lệ', ['data' => $data]);
                return ResponseApi::dataNotfound();
            }

            // Save to database
            Song::create([
                'name' => $data['title'],
                'youtube_link' => $audio,
                'mp3_url' => $basePath . $name . '.mp3',
                'beatmap_easy' => $basePath . 'beatmaps/' . $name . '_easy.json',
                'beatmap_normal' => $basePath . 'beatmaps/' . $name . '_normal.json',
                'beatmap_hard' => $basePath . 'beatmaps/' . $name . '_hard.json',
                'price' => $params['price'],
            ]);

            return ResponseApi::success();

        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm bài hát: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return ResponseApi::internalServerError();
        }
    }
}