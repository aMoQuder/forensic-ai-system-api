<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacePredictionService
{
    protected string $baseUrl = 'https://anastamer-deepface-project.hf.space';

    public function analyzeDistortedFace($file): ?array
    {
        try {
            // الموديل يحتاج وقتاً طويلاً للمعالجة، لذا نرفع الـ Timeout
            $response = Http::timeout(300)->asMultipart()
                ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
                ->post("{$this->baseUrl}/forensicAnalysis");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('AI API Error Response:', ['body' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('AI API Exception:', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
