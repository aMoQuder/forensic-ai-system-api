<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Evidence;
use App\Http\Resources\Evidence_Resource;
use App\Models\Evidences;
use App\Models\UseCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EvidenceController extends Controller {
    public function store( Request $request ): JsonResponse {
        $request->validate( [
            'name'       => 'required|string|max:255',
            'model_used' => 'required|string',
            'case_id'    => 'required|exists:use_cases,id',
            'data'       => 'required|array',
        ] );
        $case = UseCase::where( 'user_id', Auth::id() )->find( $request->case_id );
        if ( !$case ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }
        $evidence = $case->evidences()->create( [
            'name'       => $request->name,
            'model_used' => $request->model_used,
            'case_id'    => $request->case_id,
            'data'       => $request->data,
        ] );
        return response()->json( [
            'status'  => 'success',
            'message' => 'Evidence stored successfully.',
            'evidence' => $evidence
        ], 201 );
    }

    public function update( Request $request, $evidence_id, $case_id ): JsonResponse {
        $case = UseCase::where( 'user_id', Auth::id() )->find( $case_id );
        if ( !$case ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }
        $evidence = $case->evidences()->find( $evidence_id );
        if ( !$evidence ) {
            return response()->json( [
                'status' => false,
                'msg' => 'error evidence is not found ',
                'evidence' => null
            ], 201 );
        }
        $validated = $request->validate( [
            'name' => 'required|string|min:3',
        ] );
        $evidence->update( [ 'name'=>$request->name ] );
        return response()->json( [
            'status' => true,
            'msg' => 'updated evidence successfully',
            'evidence' => new Evidence_Resource( $evidence ),
        ], 201 );
    }

    public function destroy( $evidence_id, $case_id ) {
        $case = UseCase::where( 'user_id', Auth::id() )->find( $case_id );
        if ( !$case ) {
            return response()->json( [ 'status' => false, 'message' => 'error ! use case is not found ' ], 403 );
        }
        $evidence = $case->evidences()->find( $evidence_id );
        if ( !$evidence ) {
            return response()->json( [
                'status' => false,
                'msg' => 'error evidence is not found ',
                'evidence' => null
            ], 201 );
        }
        $evidence->delete();
        return response()->json( [
            'status' => true,
            'msg' => 'deleted evidence successfully',
        ], 201 );

    }

}
