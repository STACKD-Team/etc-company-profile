<?php

namespace App\Jobs;

use App\Models\RagKnowledgeSource;
use App\Services\KnowledgeSourceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class KnowledgeIndexingJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public RagKnowledgeSource $source) {}

    public function handle(KnowledgeSourceService $service): void
    {
        $service->indexNow($this->source->refresh());
    }
}
