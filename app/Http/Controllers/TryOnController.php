<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TryOnController extends Controller
{
    private $apiBaseUrl = 'https://tryon-api.com/api/v1';

    public function processTryOn(Request $request)
    {
        Log::info('=== TRY-ON PROCESS STARTED ===');

        try {
            // Validate uploads
            $request->validate([
                'user_image' => 'required|file|image|max:5120',
                'outfit_image' => 'required|file|image|max:5120',
            ]);

            $apiKey = env('TRYON_API_KEY');
            if (!$apiKey) {
                throw new \Exception('TRYON_API_KEY not found in environment');
            }

            // Prepare files
            $userImage = $request->file('user_image');
            $outfitImage = $request->file('outfit_image');

            Log::info('Sending request to external API...');

            // Build multipart data
            $multipart = [
                [
                    'name' => 'person_images',
                    'contents' => fopen($userImage->getRealPath(), 'r'),
                    'filename' => $userImage->getClientOriginalName()
                ],
                [
                    'name' => 'garment_images',
                    'contents' => fopen($outfitImage->getRealPath(), 'r'),
                    'filename' => $outfitImage->getClientOriginalName()
                ]
            ];

            // Add fast_mode if present
            if ($request->has('fast_mode') && in_array($request->input('fast_mode'), ['true', 'false'])) {
                $multipart[] = [
                    'name' => 'fast_mode',
                    'contents' => $request->input('fast_mode')
                ];
            }

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(30)->asMultipart()->post($this->apiBaseUrl . '/tryon', $multipart);

            Log::info('External API response status: ' . $response->status());

            if (!$response->successful()) {
                Log::error('API request failed: ' . $response->body());
                return response()->json([
                    'error' => 'External API error: ' . $response->status()
                ], 500);
            }

            $data = $response->json();

            Log::info('Try-on job created: ' . ($data['jobId'] ?? 'unknown'));

            return response()->json([
                'jobId' => $data['jobId'],
                'statusUrl' => $data['statusUrl'] ?? '/tryon/status/' . $data['jobId']
            ]);
        } catch (\Exception $e) {
            Log::error('TRY-ON ERROR: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($jobId)
    {
        Log::info('Checking job status: ' . $jobId);

        $apiKey = env('TRYON_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API key not configured'], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->timeout(10)->get($this->apiBaseUrl . '/tryon/status/' . $jobId);

            Log::info('Status API response: ' . $response->status());

            if (!$response->successful()) {
                Log::warning('Status check failed for job: ' . $jobId);
                return response()->json([
                    'status' => 'processing',
                    'message' => 'Job is still processing.'
                ]);
            }

            $data = $response->json();

            Log::info('Job status: ' . ($data['status'] ?? 'unknown'));

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Status check error for job ' . $jobId . ': ' . $e->getMessage());
            return response()->json([
                'status' => 'processing',
                'message' => 'Checking status...'
            ]);
        }
    }
}
