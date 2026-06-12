<?php

namespace App\Filament\Resources\RagKnowledgeSources\Pages;

use App\Filament\Resources\RagKnowledgeSources\RagKnowledgeSourceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRagKnowledgeSource extends EditRecord
{
    protected static string $resource = RagKnowledgeSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
