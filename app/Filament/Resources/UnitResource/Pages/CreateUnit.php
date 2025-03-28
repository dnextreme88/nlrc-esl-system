<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\UnitResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name'], '-');

        return $data;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
