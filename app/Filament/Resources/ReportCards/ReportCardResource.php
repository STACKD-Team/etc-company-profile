<?php

namespace App\Filament\Resources\ReportCards;

use App\Filament\Resources\ReportCards\Pages\CreateReportCard;
use App\Filament\Resources\ReportCards\Pages\EditReportCard;
use App\Filament\Resources\ReportCards\Pages\ListReportCards;
use App\Filament\Resources\ReportCards\Pages\ViewReportCard;
use App\Filament\Resources\ReportCards\Schemas\ReportCardForm;
use App\Filament\Resources\ReportCards\Schemas\ReportCardInfolist;
use App\Filament\Resources\ReportCards\Tables\ReportCardsTable;
use App\Models\ReportCard;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReportCardResource extends Resource
{
    protected static ?string $model = ReportCard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    public static function form(Schema $schema): Schema
    {
        return ReportCardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReportCardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReportCardsTable::configure($table);
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
            'index' => ListReportCards::route('/'),
            'create' => CreateReportCard::route('/create'),
            'view' => ViewReportCard::route('/{record}'),
            'edit' => EditReportCard::route('/{record}/edit'),
        ];
    }
}
