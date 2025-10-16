<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class LoginController extends Controller
{

    public function __construct(private GoogleAuthService $googleAuthService)
    {
    }

    public function redirect(Request $request)
    {
        return ResponseApi::success(
            $this->googleAuthService->getOAuthUrl()
        );
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

            return redirect()->away(env('APP_URL') . "/auth?code=$token");
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return ResponseApi::internalServerError();
        }
    }
}
