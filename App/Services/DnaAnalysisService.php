<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DnaAnalysisService
{
    protected string $baseUrl = 'https://anastamer-deepface-project.hf.space';

    public function analyzeDna(?string $sequence, $file = null, string $panel = 'all'): ?array
    {
        try {
            $request = Http::timeout(180)->asMultipart();

            if ($file) {
                $request->attach(
                    'file',
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                );
            }

            $response = $request->post("{$this->baseUrl}/dnaPhenotyping", [
                ['name' => 'sequence', 'contents' => $sequence ?? ''],
                ['name' => 'panel', 'contents' => $panel],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('DNA API Error:', ['status' => $response->status()]);
            return null;

        } catch (\Exception $e) {
            Log::error('DNA API Exception: ' . $e->getMessage());
            return null;
        }
    }
}
