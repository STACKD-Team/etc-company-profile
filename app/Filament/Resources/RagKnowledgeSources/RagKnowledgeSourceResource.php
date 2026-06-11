<?php

namespace App\Filament\Resources\RagKnowledgeSources;

use App\Filament\Resources\RagKnowledgeSources\Pages\CreateRagKnowledgeSource;
use App\Filament\Resources\RagKnowledgeSources\Pages\EditRagKnowledgeSource;
use App\Filament\Resources\RagKnowledgeSources\Pages\ListRagKnowledgeSources;
use App\Filament\Resources\RagKnowledgeSources\Pages\ViewRagKnowledgeSource;
use App\Filament\Resources\RagKnowledgeSources\Schemas\RagKnowledgeSourceForm;
use App\Filament\Resources\RagKnowledgeSources\Schemas\RagKnowledgeSourceInfolist;
use App\Filament\Resources\RagKnowledgeSources\Tables\RagKnowledgeSourcesTable;
use App\Models\RagKnowledgeSource;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RagKnowledgeSourceResource extends Resource
{
    protected static ?string $model = RagKnowledgeSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Integrasi';

    protected static ?string $navigationLabel = 'Knowledge Sources';

    protected static ?string $modelLabel = 'Knowledge Source';

    protected static ?string $pluralModelLabel = 'Knowledge Sources';

    public static function form(Schema $schema): Schema
    {
        return RagKnowledgeSourceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RagKnowledgeSourceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RagKnowledgeSourcesTable::configure($table);
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
            'index' => ListRagKnowledgeSources::route('/'),
            'create' => CreateRagKnowledgeSource::route('/create'),
            'view' => ViewRagKnowledgeSource::route('/{record}'),
            'edit' => EditRagKnowledgeSource::route('/{record}/edit'),
        ];
    }
}
