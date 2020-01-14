<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 3/25/2019
 * Time: 09:00
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if(Auth::user()->role == 1){
            return $next($request);
        } else {
            return response()->json([
                'code' => 403,
                'message' => 'Permission denied'
            ]);
        }

    }
}