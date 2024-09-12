<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class HandleExpiredToken
{
    public function handle($request, Closure $next)
    {
        try {
            if (! $user = Auth::guard('api')->user()) {
                return response()->json(['success' => false, 'message' => 'Token not provided'], 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Token invalid'], 401);
        }

        return $next($request);
    }
}
