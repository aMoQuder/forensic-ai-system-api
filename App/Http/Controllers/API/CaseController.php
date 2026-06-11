<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Usecase_Resource;
use App\Models\Evidences;
use App\Models\UseCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CaseController extends Controller {

    public function index( Request $request ): JsonResponse {
        $cases = UseCase::where( 'user_id', Auth::id() )
        ->get();
        return response()->json( [
            'status' => true,
            'user_name'=>Auth::user()->name,
            'msg' => 'cases retrived successfully by status',
            'cases' => Usecase_Resource::collection( $cases ),
        ], 200 );
    }

    public function store( Request $request ): JsonResponse {
        $validated = $request->validate( [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
        ] );
        if ( !$validated ) {
            return response()->json( [
                'status' => false,
                'msg' => 'Validation failed',
                'errors' => $validated,
            ], 400 );
        }
        $useCase = UseCase::create( [
            'name' => $request->name,
            'description' =>$request->description,
            'user_id' => Auth::id(),
        ] );
        $useCase->refresh();
        return response()->json( [
            'status' => true,
            'message' => 'Use case created successfully',
            'data' => $useCase
        ], 201 );
    }

    public function show( $case_id ): JsonResponse {
        $useCase = UseCase::where( 'user_id', Auth::id() )->find( $case_id );
        if ( !$useCase ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }
        $evidence=Evidences::where('case_id',$case_id)->get();
        return response()->json( [
            'status' => true,
            'msg'=>'use case is showed successfully ',
            'data' => new Usecase_Resource( $useCase->load( 'evidences' ) ),
            'evidence' => $evidence
        ] );
    }

    public function update( Request $request, $case_id ): JsonResponse {

        $useCase = UseCase::where( 'user_id', Auth::id() )->find( $case_id );
        if ( !$useCase ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }

        $validated = $request->validate( [
            'name' => 'required|string|max:2000',
            'description' => 'required',
        ] );
        if ( !$validated ) {
            return response()->json( [
                'status' => false,
                'msg' => 'Validation failed',
                'errors' => $validated,
            ], 400 );
        }

        $useCase->update( [
            'name' => $request->name,
            'description' =>$request->description,
        ] );
        $useCase->refresh();
        return response()->json( [
            'status' => true,
            'message' => 'Updated successfully',
            'data' => $useCase,
        ] );
    }

    public function destroy( Request $request, $case_id ): JsonResponse {
        $useCase = UseCase::where( 'user_id', Auth::id() )->find( $case_id );
        if ( !$useCase ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }
        $useCase->evidences()->delete();
        $useCase->delete();
        return response()->json( [
            'status' => true,
            'message' => 'Deleted successfully'
        ] );
    }

    public function toggle($case_id): JsonResponse {
        $useCase = UseCase::where('user_id', Auth::id())->find($case_id);

        if (!$useCase) {
            return response()->json(['status' => false, 'message' => 'Error! Use case not found'], 403);
        }
        $newStatus = ($useCase->status === 'active') ? 'complete' : 'active';
        $useCase->update([
            'status' => $newStatus,
        ]);
        $message = ($newStatus === 'active') ? 'The case is active now' : 'The case is complete now';
        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => $message
        ]);
    }

}
