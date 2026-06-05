<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForensicAiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://anastamer-deepface-project.hf.space';
    }

    public function askForensicAssistant(string $query): ?array
    {
        try {
            $response = Http::timeout(180)->post("{$this->baseUrl}/forensicAssistant", [
                "query" => $query,
                "case_context" => "string",
                "language" => "auto",
                "include_tavily" => true,
                "max_pubmed_results" => 5,
                "max_tavily_results" => 5
            ]);
            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Forensic AI API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Forensic AI API Exception: ' . $e->getMessage());
            return null;
        }
    }
}
