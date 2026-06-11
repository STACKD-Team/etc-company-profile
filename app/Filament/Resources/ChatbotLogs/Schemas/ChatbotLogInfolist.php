<?php

namespace App\Filament\Resources\ChatbotLogs\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChatbotLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('session_id'),
                TextEntry::make('user.name')
                    ->label('User')
                    ->placeholder('-'),
                TextEntry::make('user_message')
                    ->columnSpanFull(),
                TextEntry::make('bot_response')
                    ->columnSpanFull(),
                TextEntry::make('intent')
                    ->placeholder('-'),
                IconEntry::make('is_helpful')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
