<?php

namespace App\Filament\Resources\ProgramPromotions\Pages;

use App\Filament\Resources\ProgramPromotions\ProgramPromotionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramPromotion extends ViewRecord
{
    protected static string $resource = ProgramPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
