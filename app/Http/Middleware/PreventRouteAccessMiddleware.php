<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class PreventRouteAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $roleId = Auth::user()->role_id;
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        if ($roleId != 1 && $userType != 2) {

            $routes = \App\Models\User::leftJoin('permission_role', 'permission_role.role_id', '=', 'users.role_id')
                ->leftJoin('permissions', 'permissions.id', '=', 'permission_role.permission_id')
                ->where('users.id', $userId)
                ->pluck('permissions.permission_route')
                ->toArray();

            if (request()->id) {
                $path = str_replace(request()->id, '*', $request->path());
            } elseif (request()->token) {
                $path = str_replace(request()->token, '*', $request->path());
            } elseif (request()->lang_id) {
                $path = str_replace(request()->lang_id, '*', $request->path());
            } else {
                $path = $request->path();
            }
            if (in_array(str_replace(config('app.adminPrefix'), '', $path), $routes)) {
                return $next($request);
            } else {
                // return redirect(config('app.adminPrefix').'/access-denied');
                return abort(403);
            }
        }

        return $next($request);
    }
}
