<?php

namespace App\Filament\Resources\RagKnowledgeSources\Pages;

use App\Filament\Resources\RagKnowledgeSources\RagKnowledgeSourceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRagKnowledgeSource extends ViewRecord
{
    protected static string $resource = RagKnowledgeSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
