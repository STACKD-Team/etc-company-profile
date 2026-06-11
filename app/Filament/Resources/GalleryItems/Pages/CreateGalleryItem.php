<?php

namespace App\Filament\Resources\GalleryItems\Pages;

use App\Filament\Resources\GalleryItems\GalleryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryItem extends CreateRecord
{
    protected static string $resource = GalleryItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'gallery';

        return $data;
    }
}
