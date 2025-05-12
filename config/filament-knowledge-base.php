<?php

use Guava\FilamentKnowledgeBase\Models\FlatfileDocumentation;

// Config for Guava/KnowledgeBasePanel
return [
    'cache' => [
        'prefix' => env('FILAMENT_KB_CACHE_PREFIX', 'filament_kb_'),
        'ttl' => env('FILAMENT_KB_CACHE_TTL', 'forever'),
    ],
    'docs-path' => env('FILAMENT_KB_DOCS_PATH', 'docs'),
    'model' => FlatfileDocumentation::class,
    'panel' => [
        'id' => env('FILAMENT_KB_ID', 'knowledge-base'),
        'path' => env('FILAMENT_KB_PATH', 'admin/help'),
    ],
];
