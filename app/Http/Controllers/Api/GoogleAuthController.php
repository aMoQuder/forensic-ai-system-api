<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller {
    /**
    * معالجة تسجيل الدخول باستخدام Access Token من جوجل
    */

    public function handleGoogleLogin( Request $request ) {
        // استقبال التوكن من الطلب
        $googleToken = $request->input( 'access_token' );
        if ( !$googleToken ) {
            return response()->json( [
                'status' => 'error',
                'message' => 'Token is required'
            ], 400 );
        }

        try {

            $googleUser = Socialite::driver( 'google' )->stateless()->userFromToken( $googleToken );

            $data = [
                'name'  => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
            ];

            $validator = validator( $data, [
                'name'  => 'required|string|max:255',
                'email' => 'required|email',
            ] );

            if ( $validator->fails() ) {
                return response()->json( [
                    'msg' => 'validation required',
                    'status' => 0,
                    'data' => $validator->errors()
                ], 422 );
            }
            // البحث عن المستخدم باستخدام الإيميل
            $user = User::where( 'email', $googleUser->getEmail() )->first();

            if ( !$user ) {
                // إنشاء مستخدم جديد في حالة أول تسجيل دخول
                $user = User::create( [
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password'          => Hash::make( Str::random( 24 ) ),
                ] );
            } else {
                // تحديث الـ google_id للمستخدم الحالي إذا لم يكن مسجلاً
                $user->update( [
                    'google_id' => $googleUser->getId()
                ] );
            }

            // إصدار توكن Sanctum الخاص بتطبيقك
            $device_name = $request->header( 'User-Agent' ) ?: 'auth_token';
            $token = $user->createToken( $device_name )->plainTextToken;

            return response()->json( [
                'status'  => 'success',
                'message' => 'User authenticated successfully',
                'data'    => [
                    'user'         => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                    ],
                    'access_token' => $token,
                    'token_type'   => 'Bearer',
                ]
            ] );

        } catch ( \Exception $e ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'Authentication Failed',
                'debug'   => $e->getMessage()
            ], 401 );
        }
    }
}
