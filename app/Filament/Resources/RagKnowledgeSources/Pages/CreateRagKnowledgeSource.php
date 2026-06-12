<?php

namespace App\Filament\Resources\RagKnowledgeSources\Pages;

use App\Filament\Resources\RagKnowledgeSources\RagKnowledgeSourceResource;
use App\Models\RagKnowledgeSource;
use App\Services\KnowledgeSourceService;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateRagKnowledgeSource extends CreateRecord
{
    protected static string $resource = RagKnowledgeSourceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $file = $data['source_file'] ?? null;

        if ($file instanceof TemporaryUploadedFile) {
            return app(KnowledgeSourceService::class)->createFromUpload(
                $data,
                $file,
                auth()->id(),
            );
        }

        return RagKnowledgeSource::query()->create([
            ...$data,
            'uploaded_by' => auth()->id(),
            'status' => filled($data['extracted_text'] ?? null) ? 'ready' : 'draft',
        ]);
    }
}
