<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService
{
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get($this->url('/health'));

            return $response->successful() && ($response->json('status') === 'ok');
        } catch (ConnectionException) {
            return false;
        }
    }

    /**
     * @param  array<int, string>  $base64Images
     * @return array{embedding: array<int, float>, samples_used: int, samples_failed: int}|null
     */
    public function extractEmbeddingBatch(array $base64Images): ?array
    {
        try {
            $response = Http::timeout(config('facenet.timeout'))
                ->post($this->url('/extract-embedding-batch'), [
                    'images' => $base64Images,
                ]);

            if ($response->failed()) {
                Log::warning('FaceNet batch extraction failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return [
                'embedding' => $response->json('embedding'),
                'samples_used' => (int) $response->json('samples_used', 0),
                'samples_failed' => (int) $response->json('samples_failed', 0),
            ];
        } catch (ConnectionException $e) {
            Log::error('FaceNet service unreachable', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<int, float>  $captured
     * @param  array<int, array{id: int, name: string, embedding: array<int, float>}>  $knownUsers
     * @return array{user_id: int, name: string, similarity: float}|null
     */
    public function findBestMatch(array $captured, array $knownUsers): ?array
    {
        $threshold = config('facenet.threshold');
        $bestMatch = null;
        $highestSimilarity = -1.0;

        foreach ($knownUsers as $user) {
            $similarity = $this->cosineSimilarity($captured, $user['embedding']);

            if ($similarity > $highestSimilarity) {
                $highestSimilarity = $similarity;
                $bestMatch = $user;
            }
        }

        if ($bestMatch === null || $highestSimilarity < $threshold) {
            return null;
        }

        return [
            'user_id' => $bestMatch['id'],
            'name' => $bestMatch['name'],
            'similarity' => round($highestSimilarity, 4),
        ];
    }

    /**
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $len = min(count($a), count($b));

        for ($i = 0; $i < $len; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0.0 || $normB == 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    private function url(string $path): string
    {
        return rtrim(config('facenet.service_url'), '/').$path;
    }
}
