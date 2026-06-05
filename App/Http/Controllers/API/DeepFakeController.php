<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\DeepFake;
use App\Models\Evidence;
use App\Models\model_ai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DeepFakeController extends Controller {

    public function store( Request $request ) {
        $validated = $request->validate( [
            'image' => 'required|image|max:4096',
        ] );

        $imageName = '';
        $folder = 'DeepFake/img/';
        if ( $request->hasFile( 'image' ) ) {
            $image = $request->image;
            $imageName = rand( 1, 1000 ) . time() . '.' . $image->extension();
            $image->move( public_path( $folder ), $imageName );
            $imageName = $folder . $imageName;
        }
        try {
            $response = Http::timeout( 180 )
            ->attach(
                'file',
                file_get_contents( $imageName ),
                $imageName
            )->post( 'https://anastamer-deepface-communicated.hf.space/liveness', [
                'model_name'=>'VGG-Face'
            ] );

            $data = $response->json();
            if ( !$response->successful() ) {
                if ( isset( $data[ 'detail' ] ) ) {
                    return response()->json( [
                        'message' => $data[ 'detail' ],
                    ], 400 );
                }
                return response()->json( [
                    'message' => 'AI service error',
                    'error' => $data
                ], 500 );
            }

            if ( $data[ 'faces' ][ 0 ][ 'is_real' ] ) {
                $status = 'real';
            } else {
                $status = 'fake';
            }

            $model = model_ai::create( [
                'models'=>'deep fake'
            ] );
            return response()->json( [
                'status'  => 'success',
                'message' => 'Analysis deep fake completed successfully.',
                'data'    => [
                    'phenotypes'  => [
                        'model_used'=>$model->models,
                        'message'=>' success discover image    ',
                        'status'=>$status,
                        'image' => $imageName
                        ? asset( $imageName )
                        : null, ] ]
                    ] );

                } catch( \Exception $e ) {
                    return Response()->json( [ 'message' => 'Error: ' . $e->getMessage() ,
                ], 500 );
            }

        }
    }

