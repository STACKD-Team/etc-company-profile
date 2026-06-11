<?php

namespace App\Filament\Resources\ChatbotLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChatbotLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('session_id')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Textarea::make('user_message')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('bot_response')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('intent')
                    ->default(null),
                Toggle::make('is_helpful'),
            ]);
    }
}
