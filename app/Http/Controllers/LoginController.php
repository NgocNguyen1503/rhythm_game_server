<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Log;

class LoginController extends Controller
{

    public function __construct(private GoogleAuthService $googleAuthService)
    {
    }

    public function redirect(Request $request)
    {
        $state = !blank($request->input('state')) ? $request->input('state') : null;
        $url = $this->googleAuthService->getOAuthUrl($state);

        return ResponseApi::success($url);
    }

    public function callback(Request $request)
    {
        $params = $request->all();
        try {
            $accessToken = $this->googleAuthService->getAccessToken($params['code']);
            $userInfo = $this->googleAuthService->getUserInfo($accessToken);

            $user = User::createOrFirst(['email' => $userInfo['email']], [
                'name' => $userInfo['name'],
                'email' => $userInfo['email'],
                'avatar' => $userInfo['picture'],
                'role' => 2,
            ]);
            $token = $user->createToken($user->email)->plainTextToken;
            if (!blank($params['state'])) {
                Cache::put($params['state'], $token, now()->addMinutes(5));
            }

            return redirect()->away(env('APP_URL') . "/auth?code=$token");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return ResponseApi::internalServerError();
        }
    }

    public function getToken(Request $request)
    {
        $params = $request->all();
        if (!Cache::has($params['state'])) {
            return ResponseApi::forbidden();
        }
        return ResponseApi::success(Cache::get($params['state']));
    }
}
