<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterSave(): void
    {
        $announcement = $this->getRecord();
        $announcement->slug = $announcement->id. '-' .$announcement->slug;
        $announcement->save();
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit ' .$record->title;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = Auth::user()->id;
        $data['slug'] = Str::slug($data['title'], '-');

        return $data;
    }
}
