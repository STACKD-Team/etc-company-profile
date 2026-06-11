<?php

namespace App\Filament\Resources\ChatbotLogs;

use App\Filament\Resources\ChatbotLogs\Pages\CreateChatbotLog;
use App\Filament\Resources\ChatbotLogs\Pages\EditChatbotLog;
use App\Filament\Resources\ChatbotLogs\Pages\ListChatbotLogs;
use App\Filament\Resources\ChatbotLogs\Pages\ViewChatbotLog;
use App\Filament\Resources\ChatbotLogs\Schemas\ChatbotLogForm;
use App\Filament\Resources\ChatbotLogs\Schemas\ChatbotLogInfolist;
use App\Filament\Resources\ChatbotLogs\Tables\ChatbotLogsTable;
use App\Models\ChatbotLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ChatbotLogResource extends Resource
{
    protected static ?string $model = ChatbotLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ChatbotLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChatbotLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatbotLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatbotLogs::route('/'),
            'create' => CreateChatbotLog::route('/create'),
            'view' => ViewChatbotLog::route('/{record}'),
            'edit' => EditChatbotLog::route('/{record}/edit'),
        ];
    }
}
