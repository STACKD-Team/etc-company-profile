<?php

return [
    'nvidia' => [
        'api_key' => env('NVIDIA_API_KEY'),
        'base_url' => env('NVIDIA_BASE_URL', 'https://integrate.api.nvidia.com/v1'),
        'model' => env('NVIDIA_MODEL', 'deepseek-ai/deepseek-v4-flash'),
        'embedding_model' => env('NVIDIA_EMBEDDING_MODEL', 'nvidia/nv-embedqa-e5-v5'),
    ],
    'qdrant' => [
        'url' => env('QDRANT_URL'),
        'api_key' => env('QDRANT_API_KEY'),
        'collection' => env('QDRANT_COLLECTION', 'etc_planet_knowledge'),
    ],
    'chunk_size' => (int) env('RAG_CHUNK_SIZE', 1000),
    'chunk_overlap' => (int) env('RAG_CHUNK_OVERLAP', 150),
    'top_k' => (int) env('RAG_TOP_K', 5),
];
