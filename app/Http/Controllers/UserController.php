<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function saveGame(Request $request)
    {
        $params = $request->all();
        DB::table('users')->where('id', Auth::id())
            ->update([
                'chapter' => $params['chapter'],
                'stage' => $params['stage'],
            ]);
        return ResponseApi::success();
    }
}
