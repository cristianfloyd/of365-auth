<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')
            ->scopes([
                'User.Read',
                // 'Directory.Read.All',
                //'GroupMember.Read.All'
            ])
            ->redirect();
    }

    public function handleMicrosoftCallback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            // Obtener grupos del usuario
            $groups = $this->getUserGroups($microsoftUser->token);

            // Obtener roles del usuario
            // $roles = $this->getUserRoles($microsoftUser->token);

            $user = User::where('email', $microsoftUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $microsoftUser->name,
                    'email' => $microsoftUser->email,
                    'microsoft_id' => $microsoftUser->id,
                    'avatar' => $microsoftUser->avatar,
                    'password' => bcrypt(rand(1000000, 9999999)),
                    'office_groups' => $groups,
                    // 'office_roles' => $roles,
                ]);
            } else {
                $user->update([
                    'microsoft_id' => $microsoftUser->id,
                    'avatar' => $microsoftUser->avatar,
                    'office_groups' => $groups,
                    // 'office_roles' => $roles,
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            Log::error('Error Microsoft Login: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Error al iniciar sesión con Microsoft: ' . $e->getMessage());
        }
    }

    protected function getUserGroups($accessToken)
    {
        try {
            $response = Http::withToken($accessToken)
                ->get('https://graph.microsoft.com/v1.0/me/memberOf');

            if ($response->successful()) {
                $groups = $response->json()['value'];

                // Filtrar y formatear la información de los grupos
                return collect($groups)->map(function ($group) {
                    return [
                        'id' => $group['id'],
                        'displayName' => $group['displayName'],
                        'description' => $group['description'] ?? null,
                        'mail' => $group['mail'] ?? null,
                        'groupTypes' => $group['groupTypes'] ?? [],
                    ];
                })->toArray();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error getting Microsoft groups: ' . $e->getMessage());
            return [];
        }
    }

    protected function getUserRoles($accessToken)
    {
        try {
            $response = Http::withToken($accessToken)
                ->get('https://graph.microsoft.com/v1.0/me/appRoleAssignments');

            if ($response->successful()) {
                $roles = $response->json()['value'];

                return collect($roles)->map(function ($role) {
                    return [
                        'id' => $role['id'],
                        'appRoleId' => $role['appRoleId'],
                        'resourceDisplayName' => $role['resourceDisplayName'],
                        'resourceId' => $role['resourceId'],
                    ];
                })->toArray();
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Error getting Microsoft roles: ' . $e->getMessage());
            return [];
        }
    }
}
