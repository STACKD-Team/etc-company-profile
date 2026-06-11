<?php

namespace App\Filament\Resources\ChatbotLogs\Pages;

use App\Filament\Resources\ChatbotLogs\ChatbotLogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditChatbotLog extends EditRecord
{
    protected static string $resource = ChatbotLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
