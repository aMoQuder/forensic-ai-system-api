<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class SystemLoglController extends Controller {
    public function store( $user_id,  $massage ): JsonResponse {
        $user = Auth::user();
        $name = $user->name;
        $systemlog = SystemLog::create( [
            'name'=>$name,
            'massage' =>$massage
        ] );
        return response()->json( [
            'success' => true,
            'msg' => 'systemlog created successfully',
            'data' => $systemlog,
        ], 201 );
    }
}
