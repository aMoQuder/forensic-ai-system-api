<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DnaAnalysis;
use App\Models\model_ai;
use App\Services\DnaAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DnaController extends Controller {
    protected DnaAnalysisService $dnaService;

    public function __construct( DnaAnalysisService $dnaService ) {
        $this->dnaService = $dnaService;
    }

    public function processSequence( Request $request ): JsonResponse {
        $request->validate( [
            'sequence' => 'required_without:file|prohibits:file|nullable|string',
            'file'     => 'required_without:sequence|prohibits:sequence|nullable|file',
            'panel'    => 'nullable|string|in:all',
        ], [
            'sequence.required_without' => 'Please provide either a DNA sequence or a file.',
            'sequence.prohibits'        => 'Sequence text cannot be sent alongside a file.',
            'file.prohibits'            => 'File upload is prohibited when a sequence string is provided.',
        ] );

        $sequence = $request->input( 'sequence' );
        $file = $request->file( 'file' );
        $panel = $request->input( 'panel', 'all' );

        $aiResponse = $this->dnaService->analyzeDna( $sequence, $file, $panel );

        if ( !$aiResponse || !isset( $aiResponse[ 'predictions' ] ) ) {
            return response()->json( [
                'status'  => 'error',
                'message' => 'AI model connection failed or invalid response.'
            ], 500 );
        }

        $predictions = $aiResponse[ 'predictions' ];

        $bestEyeColor  = $this->getHighestTrait( $predictions[ 'eye_color' ] ?? [] );
        $bestHairColor = $this->getHighestTrait( $predictions[ 'hair_color' ] ?? [] );
        $bestSkinColor = $this->getHighestTrait( $predictions[ 'skin_color' ] ?? [] );
        $bestAncestry  = $this->getHighestTrait( $predictions[ 'ancestry' ] ?? [] );
        $model = model_ai::create( [
            'models'=>'dna model'
        ] );
        return response()->json( [
            'status'  => 'success',
            'message' => 'Analysis completed successfully.',
            'model_used'=>$model->models,

            'data'    => [
                'phenotypes'  => [
                    'eye_color'  => $bestEyeColor,
                    'hair_color' => $bestHairColor,
                    'skin_color' => $bestSkinColor,
                    'ancestry'   => $bestAncestry,
                ]
            ]
        ] );
    }

    private function getHighestTrait( array $traits ): array {
        if ( empty( $traits ) ) {
            return [ 'trait' => 'Unknown', 'probability' => 0 ];
        }

        $maxValue = max( $traits );
        $maxTrait = array_search( $maxValue, $traits );

        return [
            'trait'       => $maxTrait,
            'probability' => $maxValue
        ];
    }

    public function history( Request $request ): JsonResponse {
        $history = $request->user()->dnaAnalyses()
        ->select( 'id', 'eye_color', 'hair_color', 'skin_color', 'ancestry', 'created_at' )
        ->latest()
        ->paginate( 10 );

        return response()->json( $history );
    }
}
