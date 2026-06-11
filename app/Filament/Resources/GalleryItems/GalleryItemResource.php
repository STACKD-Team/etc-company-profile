<?php

namespace App\Filament\Resources\GalleryItems;

use App\Filament\Resources\Contents\Schemas\ContentForm;
use App\Filament\Resources\Contents\Schemas\ContentInfolist;
use App\Filament\Resources\Contents\Tables\ContentsTable;
use App\Filament\Resources\GalleryItems\Pages\CreateGalleryItem;
use App\Filament\Resources\GalleryItems\Pages\EditGalleryItem;
use App\Filament\Resources\GalleryItems\Pages\ListGalleryItems;
use App\Filament\Resources\GalleryItems\Pages\ViewGalleryItem;
use App\Models\Content;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GalleryItemResource extends Resource
{
    protected static ?string $model = Content::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'Gallery Item';

    protected static ?string $pluralModelLabel = 'Gallery';

    protected static ?string $slug = 'gallery-items';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'gallery');
    }

    public static function form(Schema $schema): Schema
    {
        return ContentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGalleryItems::route('/'),
            'create' => CreateGalleryItem::route('/create'),
            'view' => ViewGalleryItem::route('/{record}'),
            'edit' => EditGalleryItem::route('/{record}/edit'),
        ];
    }
}
