<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Resources\Partners\PartnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'partner';

        return $data;
    }
}
