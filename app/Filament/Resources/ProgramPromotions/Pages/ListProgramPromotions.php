<?php

namespace App\Filament\Resources\ProgramPromotions\Pages;

use App\Filament\Resources\ProgramPromotions\ProgramPromotionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramPromotions extends ListRecords
{
    protected static string $resource = ProgramPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
