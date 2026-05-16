<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\UseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function me()
    {


        $numcase=UseCase::where('user_id',Auth::user()->id)->count();
        return response()->json([
            'status' => true,
            'message' => 'User data retrieved successfully',
            'data' =>new SettingResource(Auth::user()),
            'case count'=>$numcase,
        ]);
    }
}
