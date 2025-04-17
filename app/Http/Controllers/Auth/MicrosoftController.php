<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Información básica del usuario
            $userInfo = [
                'id' => $microsoftUser->id,
                'name' => $microsoftUser->name,
                'email' => $microsoftUser->email,
                'avatar' => $microsoftUser->avatar,
                'nickname' => $microsoftUser->nickname,
                'token' => $microsoftUser->token,
                'refreshToken' => $microsoftUser->refreshToken,
                'expiresIn' => $microsoftUser->expiresIn,
            ];

            // Mostrar la información en el log
            Log::info('Microsoft User Info:', $userInfo);

            $user = User::where('email', $microsoftUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $microsoftUser->name,
                    'email' => $microsoftUser->email,
                    'microsoft_id' => $microsoftUser->id,
                    'avatar' => $microsoftUser->avatar,
                    'password' => bcrypt(rand(1000000, 9999999)),
                ]);
            } else {
                $user->update([
                    'microsoft_id' => $microsoftUser->id,
                    'avatar' => $microsoftUser->avatar,
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Microsoft: ' . $e->getMessage());
        }
    }
}
