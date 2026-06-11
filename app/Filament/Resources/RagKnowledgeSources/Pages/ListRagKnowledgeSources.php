<?php

namespace App\Filament\Resources\RagKnowledgeSources\Pages;

use App\Filament\Resources\RagKnowledgeSources\RagKnowledgeSourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRagKnowledgeSources extends ListRecords
{
    protected static string $resource = RagKnowledgeSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
