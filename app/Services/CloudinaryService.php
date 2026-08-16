<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryService
{
    public function upload(
        UploadedFile $file,
        ?string $folder = null
    ): array {
        $this->ensureConfigured();

        $parameters = [
            'folder' => $folder
                ?: (string) config('services.cloudinary.folder'),

            'overwrite' => 'false',
            'timestamp' => (string) time(),
            'unique_filename' => 'true',
            'use_filename' => 'true',
        ];

        $stream = fopen($file->getRealPath(), 'r');

        if ($stream === false) {
            throw new RuntimeException(
                'The selected file could not be read.'
            );
        }

        try {
            $response = Http::timeout(90)
                ->attach(
                    'file',
                    $stream,
                    $file->getClientOriginalName()
                )
                ->post(
                    $this->endpoint('auto/upload'),
                    [
                        ...$parameters,

                        'api_key' => config(
                            'services.cloudinary.api_key'
                        ),

                        'signature' => $this->signature(
                            $parameters
                        ),
                    ]
                );
        } finally {
            fclose($stream);
        }

        $this->ensureSuccessful($response, 'upload');

        $payload = $response->json();

        foreach (
            ['public_id', 'secure_url', 'resource_type', 'bytes']
            as $field
        ) {
            if (! isset($payload[$field])) {
                throw new RuntimeException(
                    'Cloudinary returned an incomplete upload response.'
                );
            }
        }

        return $payload;
    }

    public function destroy(
        string $publicId,
        string $resourceType
    ): void {
        $this->ensureConfigured();

        $parameters = [
            'invalidate' => 'true',
            'public_id' => $publicId,
            'timestamp' => (string) time(),
        ];

        $response = Http::asForm()
            ->timeout(30)
            ->post(
                $this->endpoint($resourceType.'/destroy'),
                [
                    ...$parameters,

                    'api_key' => config(
                        'services.cloudinary.api_key'
                    ),

                    'signature' => $this->signature(
                        $parameters
                    ),
                ]
            );

        $this->ensureSuccessful($response, 'delete');

        if (
            ! in_array(
                $response->json('result'),
                ['ok', 'not found'],
                true
            )
        ) {
            throw new RuntimeException(
                'Cloudinary could not delete the stored file.'
            );
        }
    }

    private function signature(array $parameters): string
    {
        ksort($parameters);

        $signatureBase = collect($parameters)
            ->map(
                fn ($value, $key) => $key.'='.$value
            )
            ->implode('&');

        return sha1(
            $signatureBase.config(
                'services.cloudinary.api_secret'
            )
        );
    }

    private function endpoint(string $path): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s',

            rawurlencode(
                (string) config(
                    'services.cloudinary.cloud_name'
                )
            ),

            $path
        );
    }

    private function ensureConfigured(): void
    {
        if (
            ! config('services.cloudinary.cloud_name')
            || ! config('services.cloudinary.api_key')
            || ! config('services.cloudinary.api_secret')
        ) {
            throw new RuntimeException(
                'Cloudinary is not configured. Add the cloud name, API key and API secret to the .env file.'
            );
        }
    }

    private function ensureSuccessful(
        Response $response,
        string $action
    ): void {
        if ($response->successful()) {
            return;
        }

        $message = $response->json('error.message');

        throw new RuntimeException(
            $message
                ? 'Cloudinary could not '.$action.' the file: '.$message
                : 'Cloudinary could not '.$action.' the file. Please try again.'
        );
    }
}