<?php

namespace App\Filament\Resources\ChatbotLogs\Pages;

use App\Filament\Resources\ChatbotLogs\ChatbotLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewChatbotLog extends ViewRecord
{
    protected static string $resource = ChatbotLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
