<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use RuntimeException;

class MediaStorageService
{
    public function putUploadedFile(UploadedFile $file, string $directory): string
    {
        $path = $this->buildPath($file, $directory);
        $this->uploadToFirebase($file, $path);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $this->deleteFromFirebase($path);
    }

    public function replace(?string $oldPath, UploadedFile $file, string $directory): string
    {
        $path = $this->putUploadedFile($file, $directory);
        $this->delete($oldPath);

        return $path;
    }

    protected function buildPath(UploadedFile $file, string $directory): string
    {
        $directory = trim($directory, '/');
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';

        return $directory.'/'.Str::uuid().'.'.$extension;
    }

    protected function uploadToFirebase(UploadedFile $file, string $path): void
    {
        if (! $this->hasFirebaseStorage()) {
            $stream = fopen($file->getRealPath(), 'r');

            if ($stream === false) {
                throw new RuntimeException('Unable to open uploaded file for local storage upload.');
            }

            try {
                Storage::disk('public')->put($path, $stream);
            } finally {
                fclose($stream);
            }

            return;
        }

        $bucket = $this->bucket();
        $stream = fopen($file->getRealPath(), 'r');

        if ($stream === false) {
            throw new RuntimeException('Unable to open uploaded file for Firebase upload.');
        }

        try {
            $bucket->upload($stream, [
                'name' => $path,
                'metadata' => [
                    'contentType' => $file->getMimeType(),
                ],
            ]);
        } finally {
            fclose($stream);
        }
    }

    protected function deleteFromFirebase(string $path): void
    {
        if (! $this->hasFirebaseStorage()) {
            Storage::disk('public')->delete($path);

            return;
        }

        $object = $this->bucket()->object($path);

        if ($object->exists()) {
            $object->delete();
        }
    }

    protected function bucket(): object
    {
        $credentials = config('firebase.credentials');
        $bucketName = config('firebase.storage_bucket');

        if (! $credentials || ! $bucketName) {
            throw new RuntimeException('Firebase Storage is not configured. Set FIREBASE_CREDENTIALS and FIREBASE_STORAGE_BUCKET.');
        }

        $factory = (new Factory)->withServiceAccount($credentials);

        return $factory->createStorage()->getBucket($bucketName);
    }

    protected function hasFirebaseStorage(): bool
    {
        return class_exists('Kreait\\Firebase\\Factory')
            && (bool) config('firebase.credentials')
            && (bool) config('firebase.storage_bucket');
    }
}
