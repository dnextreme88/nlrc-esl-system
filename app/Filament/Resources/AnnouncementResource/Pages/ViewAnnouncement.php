<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnouncement extends ViewRecord
{
    protected static string $resource = AnnouncementResource::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-eye';
    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected function getActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return $record->title;
    }
}
