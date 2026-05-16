<?php
namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordOtpMail;

class AuthController extends Controller {


    public function ChangeData( Request $request ) {

        $user_id = Auth::id();
        $user = User::find( $user_id );

        $validatedData = validator( $request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|numeric|digits:11',
            'national_id' => 'required|numeric|digits:14',
            'date_of_birth' => 'required|date',
        ] );

        if ( $validatedData->fails() ) {
            return response()->json( [
                'msg' => 'validation required',
                'status' => 0,
                'data' => $validatedData->errors()
            ], 422 );
        }
        if ($user->email != $request->email) {
            $validatedData = validator( $request->all(), [
                'email' => 'unique:users',
            ] );
        }
        if ($user->national_id != $request->national_id) {
            $validatedData = validator( $request->all(), [
                'national_id' => 'unique:users',
            ] );
        }

        if ( $validatedData->fails() ) {
            return response()->json( [
                'msg' => 'validation required',
                'status' => 0,
                'data' => $validatedData->errors()
            ], 422 );
        }
        $imagePath = null;
        if ( $request->hasFile( 'image' ) ) {
                $imagePath = $request->file( 'image' )->store( 'posts', 'public' );
        }

        $user->update( [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'image' => $imagePath,
            'role' =>'doctor'
        ] );

        return response()->json( [
            'msg' => 'your data was updated in Forensic AI',
            'user' => new UserResource( $user ),
        ] );
    }
    public function register( Request $request ) {
        $validatedData = validator( $request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone_number' => 'required|numeric|digits:11',
            'national_id' => 'required|numeric|digits:14|unique:users',
            'date_of_birth' => 'required|date',
        ] );

        if ( $validatedData->fails() ) {
            return response()->json( [
                'msg' => 'validation required',
                'status' => 0,
                'data' => $validatedData->errors()
            ], 422 );
        }

        $user = User::create( [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make( $request->password ),
            'role' =>'doctor'
        ] );

        $token = $user->createToken( 'auth_token' )->plainTextToken;

        return response()->json( [
            'msg' => 'welcome doctor in Forensic AI',
            'user' => new UserResource( $user ),
            'token' => $token
        ] );
    }

    public function login( Request $request ) {
        $request->validate( [
            'email' => 'required|email',
            'password' => 'required'
        ] );

        if ( !Auth::attempt( $request->only( 'email', 'password' ) ) ) {
            return response()->json( [
                'message' => 'Invalid credentials'
            ], 401 );
        }

        $user = User::where( 'email', $request->email )->first();
        $token = $user->createToken( 'auth_token' )->plainTextToken;

        return response()->json( [
            'msg' => 'successfully logged in',
            'user' => new UserResource( $user ),
            'token' => $token
        ] );
    }

    public function sendResetCode( Request $request ) {
        $request->validate( [
            'email' => 'required|email|exists:users,email'
        ] );

        $otp = rand( 100000, 999999 );

        Cache::put( 'reset_otp_' . $request->email, $otp, now()->addSeconds( 60 ) );
        Mail::to( $request->email )->send( new ResetPasswordOtpMail( $otp ) );

        return response()->json( [
            'status' => 1,
            'msg' => 'Verification code sent successfully to your email.'
        ] );
    }

    public function verifyCode( Request $request ) {
        $request->validate( [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6'
        ] );

        $cachedOtp = Cache::get( 'reset_otp_' . $request->email );

        if ( !$cachedOtp ) {
            return response()->json( [
                'status' => 0,
                'msg' => 'Code has expired. Please request a new one.'
            ], 400 );
        }

        if ( $cachedOtp != $request->otp ) {
            return response()->json( [
                'status' => 0,
                'msg' => 'Invalid verification code.'
            ], 400 );
        }

        return response()->json( [
            'status' => 1,
            'msg' => 'Code verified successfully. You can now reset your password.'
        ] );
    }

    public function resetPassword( Request $request ) {
        $request->validate( [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|min:8|confirmed'
        ] );


        $user = User::where( 'email', $request->email )->first();
        $user->update( [
            'password' => Hash::make( $request->password )
        ] );

        Cache::forget( 'reset_otp_' . $request->email );

        return response()->json( [
            'status' => 1,
            'msg' => 'Password has been reset successfully. You can now login.'
        ] );
    }

    public function logout(Request $request){
        Auth::logout();
        return response()->json([
            'status' => 1,
            'msg' => 'Logout successfully.'
        ]);
    }

    public function uploadImage( Request $request ) {
        $request->validate( [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ] );
        if ( Auth::user() ) {
            $user = Auth::user();
            $imagePath = null;
            if ( $request->hasFile( 'image' ) ) {
                $imagePath = $request->file( 'image' )->store( 'posts', 'public' );
            } else {
                return response()->json( [
                    'msg' => 'Image not found'
                ] );
            }
            $user->image = $imagePath;
            $user->save();
            return response()->json( [
                'msg' => 'Image uploaded successfully',
                'image' => asset( 'storage/' . $imagePath )
            ] );
        } else {
            return response()->json( [
                'msg' => 'User not found'
            ] );
        }
    }

    public function changePassword( Request $request ) {
        $request->validate( [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ] );

        $user = Auth::user();
        if ( !Hash::check( $request->current_password, $user->password ) ) {
            return response()->json( [
                'status' => 0,
                'msg' => 'The current password you entered is incorrect.'
            ], 400 );
        }
        $user->password = Hash::make( $request->new_password );
        $user->save();
        return response()->json( [
            'status' => 1,
            'msg' => 'Your password has been changed successfully.'
        ] );
    }
}
