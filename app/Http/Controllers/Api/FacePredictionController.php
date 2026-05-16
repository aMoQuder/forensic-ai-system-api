<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FacePrediction;
use App\Models\model_ai;
use App\Services\FacePredictionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FacePredictionController extends Controller {
    protected FacePredictionService $faceService;

    public function __construct( FacePredictionService $faceService ) {
        $this->faceService = $faceService;
    }

    public function processFace( Request $request ): JsonResponse {
        $request->validate( [
            'image' => 'required|image|max:10240',
        ] );

        $aiResponse = $this->faceService->analyzeDistortedFace( $request->file( 'image' ) );

        if ( !$aiResponse || !isset( $aiResponse[ 'enhanced_image_base64' ] ) ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'AI reconstruction failed or model does not found any person in image.'
            ], 500 );
        }

        $face = $aiResponse[ 'faces' ][ 0 ] ?? null;

        $age    = $face[ 'age_info' ][ 'age_range' ] ?? 'Unknown';
        $gender = $face[ 'gender_info' ][ 'gender' ] ?? 'Unknown';

        try {
            $base64String = $aiResponse[ 'enhanced_image_base64' ];

            if ( preg_match( '/^data:image\/(\w+);base64,/', $base64String, $type ) ) {
                $base64String = substr( $base64String, strpos( $base64String, ',' ) + 1 );
            }

            $imageData = base64_decode( $base64String );

            if ( !$imageData ) {
                throw new \Exception( 'Invalid Base64 format.' );
            }

            $fileName = 'reconstructed_faces/' . Str::uuid() . '.jpg';
            Storage::disk( 'public' )->put( $fileName, $imageData );

        } catch ( \Exception $e ) {
            Log::error( 'Image Save Error:', [ 'msg' => $e->getMessage() ] );
            return response()->json( [ 'status' => 'error', 'message' => 'Failed to save processed image.' ], 500 );
        }
        $model = model_ai::create( [
            'models'=>'face reconstruction'
        ] );
        return response()->json( [
            'status'  => 'success',
            'message' => 'Analysis successful.',
            'model_used'=>$model->models,

            'data'    => [
                'phenotypes'  => [
                    'age'       => $age,
                    'gender'    => $gender,
                    'image_url' => asset( 'storage/' . $fileName ),
                ]
            ]
        ] );

    }

}
