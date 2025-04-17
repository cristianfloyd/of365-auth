<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
            
            $user = User::updateOrCreate(
                ['microsoft_id' => $microsoftUser->id],
                [
                    'name' => $microsoftUser->name,
                    'email' => $microsoftUser->email,
                    'avatar' => $microsoftUser->avatar,
                    'password' => bcrypt(rand(1000000, 9999999)),
                ]
            );
            
            Auth::login($user);
            
            return redirect('/dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Microsoft: ' . $e->getMessage());
        }
    }
}
