<?php

namespace App\Filament\Resources\ReportCards\Pages;

use App\Filament\Resources\ReportCards\ReportCardResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReportCard extends ViewRecord
{
    protected static string $resource = ReportCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
