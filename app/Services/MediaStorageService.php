<?php

namespace App\Services;

use Cloudinary\Cloudinary;
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

        if ($this->hasCloudinary()) {
            return $this->uploadToCloudinary($file, $directory, $path);
        }

        $this->uploadToFirebase($file, $path);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if ($this->hasCloudinaryPath($path)) {
            $this->deleteFromCloudinary($path);

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

    public function url(?string $path, string $resourceType = 'image'): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        if (Str::startsWith($path, 'cloudinary://')) {
            if (! $this->hasCloudinary()) {
                return null;
            }

            $publicId = $this->cloudinaryPublicId($path);

            return match ($resourceType) {
                'video' => (string) $this->cloudinary()->video($publicId)->toUrl(),
                'raw' => (string) $this->cloudinary()->raw($publicId)->toUrl(),
                default => (string) $this->cloudinary()->image($publicId)->toUrl(),
            };
        }

        if (Str::startsWith($path, ['images/', 'videos/', 'storage/'])) {
            return asset(ltrim($path, '/'));
        }

        return Storage::disk('public')->url($path);
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

    protected function uploadToCloudinary(UploadedFile $file, string $directory, string $fallbackPath): string
    {
        $folder = trim($directory, '/');
        $publicId = Str::beforeLast($fallbackPath, '.');
        $resourceType = $this->cloudinaryResourceType($file->getMimeType());

        $result = $this->cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
            'public_id' => Str::after($publicId, $folder.'/'),
            'resource_type' => $resourceType,
            'overwrite' => true,
        ]);

        $storedPublicId = $result['public_id'] ?? $publicId;
        $version = $result['version'] ?? null;

        return 'cloudinary://'.$storedPublicId.($version ? '?v='.$version : '');
    }

    protected function deleteFromCloudinary(string $path): void
    {
        $this->cloudinary()->uploadApi()->destroy($this->cloudinaryPublicId($path), [
            'resource_type' => 'auto',
            'invalidate' => true,
        ]);
    }

    protected function cloudinary(): Cloudinary
    {
        if (config('cloudinary.url')) {
            return new Cloudinary(config('cloudinary.url'));
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => (bool) config('cloudinary.secure', true),
            ],
        ]);
    }

    protected function hasCloudinary(): bool
    {
        return class_exists(Cloudinary::class)
            && (
                (bool) config('cloudinary.url')
                || ((bool) config('cloudinary.cloud_name') && (bool) config('cloudinary.api_key') && (bool) config('cloudinary.api_secret'))
            );
    }

    protected function hasCloudinaryPath(string $path): bool
    {
        return Str::startsWith($path, 'cloudinary://') && $this->hasCloudinary();
    }

    protected function cloudinaryPublicId(string $path): string
    {
        return Str::before(Str::after($path, 'cloudinary://'), '?');
    }

    protected function cloudinaryResourceType(?string $mimeType): string
    {
        return match (true) {
            Str::startsWith((string) $mimeType, 'image/') => 'image',
            Str::startsWith((string) $mimeType, 'video/') => 'video',
            default => 'raw',
        };
    }
}
