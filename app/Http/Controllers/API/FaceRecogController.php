<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\model_ai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaceRecogController extends Controller {
    public function store( Request $request ) {
        $validated = $request->validate( [
            'image' => 'required|image|max:4096',
        ] );
        $imageName = '';
        if ( $request->hasFile( 'image' ) ) {
            $image = $request->image;
            $imageName = rand( 1, 1000 ) . time() . '.' . $image->extension();
            $image->move( public_path( 'FaceRecog/img/' ), $imageName );
            $imageName = 'FaceRecog/img/'.$imageName;
        }
        try {

            $response = Http::timeout( 60 )
            ->attach(
                'file',
                file_get_contents( public_path( $imageName ) ),
                $imageName
            )
            ->post( 'https://anastamer-deepface-communicated.hf.space/recognize' );
            $data = $response->json();
            if ( !$response->successful() ) {

                if ( isset( $data[ 'detail' ] ) ) {
                    return response()->json( [
                        'message' => $data[ 'detail' ], // No face detected in the image. Please try again with a different image.
                    ], 400 );
                }
                return response()->json( [
                    'message' => 'AI service error',
                    'error' => $data
                ], 500 );
            }
            $model = model_ai::create( [
                'models'=>'face recognation'
            ] );

            return response()->json( [
                'status'  => 'success',
                'message' => 'Analysis face recognation completed successfully.',
                'model_used'=>$model->models,
                'data'    => [
                    'phenotypes'  => [
                        'name' => $data[ 'identities' ][ 0 ],
                        'image' => $imageName
                        ? asset( $imageName )
                        : null, ] ]
                    ] );

                } catch ( \Exception $e ) {
                    return response()->json( [
                        'message' => 'Error: ' . $e->getMessage()
                    ], 500 );
                }

            }
        }

