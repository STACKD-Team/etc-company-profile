<?php

namespace App\Filament\Resources\ProgramPromotions;

use App\Filament\Resources\ProgramPromotions\Pages\CreateProgramPromotion;
use App\Filament\Resources\ProgramPromotions\Pages\EditProgramPromotion;
use App\Filament\Resources\ProgramPromotions\Pages\ListProgramPromotions;
use App\Filament\Resources\ProgramPromotions\Pages\ViewProgramPromotion;
use App\Filament\Resources\ProgramPromotions\Schemas\ProgramPromotionForm;
use App\Filament\Resources\ProgramPromotions\Schemas\ProgramPromotionInfolist;
use App\Filament\Resources\ProgramPromotions\Tables\ProgramPromotionsTable;
use App\Models\ProgramPromotion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProgramPromotionResource extends Resource
{
    protected static ?string $model = ProgramPromotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProgramPromotionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProgramPromotionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramPromotionsTable::configure($table);
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
            'index' => ListProgramPromotions::route('/'),
            'create' => CreateProgramPromotion::route('/create'),
            'view' => ViewProgramPromotion::route('/{record}'),
            'edit' => EditProgramPromotion::route('/{record}/edit'),
        ];
    }
}
