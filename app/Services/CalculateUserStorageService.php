<?php

namespace App\Services;

use App\Models\User;
use Aws\S3\S3Client;

class CalculateUserStorageService
{
    /**
     * Calculate the total storage occupied by a user in S3
     *
     * @param User $user
     * @return array
     */
    public function calculate(User $user): array
    {
        $bucket = env('AWS_BUCKET');
        $region = env('AWS_DEFAULT_REGION');
        $prefix = "FaceFinder/Albums/{$user->id}/";

        // Initialize S3 Client
        $client = new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $totalBytes = 0;

        // Use paginator to handle folders with many files
        $results = $client->getPaginator('ListObjectsV2', [
            'Bucket' => $bucket,
            'Prefix' => $prefix,
        ]);

        foreach ($results as $page) {
            if (!empty($page['Contents'])) {
                foreach ($page['Contents'] as $object) {
                    $totalBytes += $object['Size'];
                }
            }
        }

        return [
            'bytes'      => $totalBytes,
            'kilobytes'  => round($totalBytes / 1024, 2),
            'megabytes'  => round($totalBytes / (1024 * 1024), 2),
            'gigabytes'  => round($totalBytes / (1024 * 1024 * 1024), 2),
        ];
    }
}

