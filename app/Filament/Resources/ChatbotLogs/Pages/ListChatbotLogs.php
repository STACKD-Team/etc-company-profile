<?php

namespace App\Filament\Resources\ChatbotLogs\Pages;

use App\Filament\Resources\ChatbotLogs\ChatbotLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatbotLogs extends ListRecords
{
    protected static string $resource = ChatbotLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
