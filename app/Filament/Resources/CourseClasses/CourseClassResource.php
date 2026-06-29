<?php

namespace App\Filament\Resources\CourseClasses;

use App\Filament\Resources\CourseClasses\Pages\CreateCourseClass;
use App\Filament\Resources\CourseClasses\Pages\EditCourseClass;
use App\Filament\Resources\CourseClasses\Pages\ListCourseClasses;
use App\Filament\Resources\CourseClasses\Pages\ViewCourseClass;
use App\Filament\Resources\CourseClasses\Schemas\CourseClassForm;
use App\Filament\Resources\CourseClasses\Schemas\CourseClassInfolist;
use App\Filament\Resources\CourseClasses\Tables\CourseClassesTable;
use App\Models\CourseClass;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseClassResource extends Resource
{
    protected static ?string $model = CourseClass::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    protected static ?string $navigationLabel = 'Classes';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CourseClassForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CourseClassInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseClassesTable::configure($table);
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
            'index' => ListCourseClasses::route('/'),
            'create' => CreateCourseClass::route('/create'),
            'view' => ViewCourseClass::route('/{record}'),
            'edit' => EditCourseClass::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
