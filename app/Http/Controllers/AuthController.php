<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Routing\Exceptions\RouteNotFoundException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 400);
            }
            // Reset refresh_attempts saat user berhasil login
            $user = auth()->guard('api')->user();

            if ($user->refresh_count >= 4 && $user->role != 'admin') {
                $user->delete(); // Hapus user jika sudah mencapai batas
                return response()->json(['message' => 'Akun Pengguna Akan Dihapus Karena Telah Mencapai Batas Maksimal Refresh Token.'], 401);
            } else {
                $user->refresh_count += 1;
                $user->save();
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could Not Create Token'], 500);
        }

        return response()->json([
            'success' => true,
            'user'    => auth()->guard('api')->user(),
            'access_token' => $token,
            'refresh_token' => JWTAuth::claims(['type' => 'refresh'])->attempt($credentials)
        ], 200);

        // return response()->json(compact('token'));
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        try {
            // Coba refresh token
            $newToken = JWTAuth::setToken($refreshToken)->refresh();

            $user = auth()->guard('api')->user();

            if ($user->refresh_count >= 4 && $user->role != 'admin') {
                $user->delete(); // Hapus user jika sudah mencapai batas
                return response()->json(['message' => 'Akun Pengguna Akan Dihapus Karena Telah Mencapai Batas Maksimal Refresh Token.'], 401);
            }

            // Increment refresh_attempts dan simpan
            $user->increment('refresh_count');
            $user->save();
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Refresh Token Kadaluarsa'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Tidak dapat memberikan Token baru'], 500);
        }

        return response()->json([
            'success' => true,
            'access_token' => $newToken
        ]);
    }

    public function user()
    {
        try {
            $user = JWTAuth::user();
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Kesalahan JWT: ' . $e->getMessage(),
            ], 500);
        } catch (RouteNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Masuk Kembali: ' . $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        //remove token
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                //return response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!',
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ada Kesalahan JWT: ' . $e->getMessage(),
            ], 500);
        } catch (RouteNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan Masuk Kembali: ' . $e->getMessage(),
            ], 401);
        }
    }

    public function checkToken()
    {
        try {
            if (auth()->guard('api')->check()) {
                return response()->json(['status' => true, 'message' => 'Token is valid']);
            } else {
                return response()->json(['status' => false, 'message' => 'Token is invalid']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Token validation failed']);
        }
    }
}
