<?php

namespace App\Filament\Resources\Reels\Pages;

use App\Filament\Resources\Reels\ReelResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReel extends EditRecord
{
    protected static string $resource = ReelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
