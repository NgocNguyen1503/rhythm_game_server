<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function __construct(private GoogleAuthService $googleAuthService)
    {
    }

    public function redirect(Request $request)
    {
        $params = $request->all();
        if (!isset($params['from']) || blank($params['from'])) {
            return ResponseApi::forbidden();
        }
        Cache::put('from', $params['from']);

        if (!isset($params['state']) || blank($params['state'])) {
            return ResponseApi::forbidden();
        }
        $state = $params['state'];
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

            if (isset($params['state']) && !blank($params['state'])) {
                Cache::put($params['state'], $token, now()->addHour());
            }

            $from = null;
            if (Cache::has('from')) {
                $from = Cache::get('from');
            }
            return match ($from) {
                'web' => redirect()->away(env('APP_URL') . "/login?code=$token"),
                'unity' => redirect('/success'),
                default => redirect('/login')
            };
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
