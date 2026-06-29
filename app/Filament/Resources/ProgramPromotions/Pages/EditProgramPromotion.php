<?php

namespace App\Filament\Resources\ProgramPromotions\Pages;

use App\Filament\Resources\ProgramPromotions\ProgramPromotionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramPromotion extends EditRecord
{
    protected static string $resource = ProgramPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
