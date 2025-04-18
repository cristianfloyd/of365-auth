<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOfficeGroup
{
    public function handle(Request $request, Closure $next, ...$groups)
    {
        if (!$request->user() || !$request->user()->office_groups) {
            return redirect()->route('unauthorized');
        }

        $userGroups = collect($request->user()->office_groups)
            ->pluck('displayName')
            ->toArray();

        $hasAccess = collect($groups)->some(function ($group) use ($userGroups) {
            return in_array($group, $userGroups);
        });

        if (!$hasAccess) {
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
