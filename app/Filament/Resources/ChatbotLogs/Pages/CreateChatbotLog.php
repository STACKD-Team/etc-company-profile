<?php

namespace App\Filament\Resources\ChatbotLogs\Pages;

use App\Filament\Resources\ChatbotLogs\ChatbotLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChatbotLog extends CreateRecord
{
    protected static string $resource = ChatbotLogResource::class;
}
