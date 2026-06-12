<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Model;

trait AuthorizesAdminResource
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}
