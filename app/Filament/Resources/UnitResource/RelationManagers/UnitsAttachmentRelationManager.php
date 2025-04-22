<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use App\Models\UnitsAttachment;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class UnitsAttachmentRelationManager extends RelationManager
{
    protected static string $relationship = 'unit_attachments';
    protected static ?string $title = 'Attachments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file_path')
                    ->acceptedFileTypes([
                        'audio/mpeg',
                        'audio/wav',
                        'audio/webm',
                        'video/mp4',
                        'video/mpeg',
                        'video/webm',
                    ])
                    ->directory('/units')
                    ->helperText('Attach any media here for this unit. Accepted extensions: .avi, .mp3, .mp4, .wav')
                    ->hidden(fn (string $operation): bool => $operation === 'edit')
                    ->label('Upload a file')
                    ->required()
                    ->visibility('public'),
                TextInput::make('file_name')
                    ->helperText('This will show up as the name on the unit detail page if users download the attachment.')
                    ->maxLength(128)
                    ->minLength(3)
                    ->required(),
                Textarea::make('description')
                    ->columnSpan(2)
                    ->maxLength(128)
                    ->minLength(5),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_type')
                    ->sortable(),
                TextColumn::make('file_path')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->searchable()
                    ->words(5),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Uploaded on')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('file_name', 'asc')
            ->emptyStateDescription('Add a new attachment by clicking the top-right button')
            ->emptyStateHeading('No attachments found for this unit')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->label('Add new attachment')
                    ->modalHeading('Add new attachment')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['file_name'] = trim($data['file_name']);
                        $data['file_type'] = strrchr($data['file_path'], '.');
                        $data['description'] = trim($data['description']);

                        return $data;
                    }),
            ])
            ->actions([
                MediaAction::make('preview')
                    ->color('info')
                    ->extraModalFooterActions([
                        Action::make('open_in_browser')
                            ->icon('heroicon-o-globe-alt')
                            ->label('Open in browser (in a new tab)')
                            // Do not change the order of this two methods so that the attachment opens on a new tab
                            ->url(fn (UnitsAttachment $record) => asset('storage/' .$record->file_path))
                            ->openUrlInNewTab(),
                    ])
                    ->icon('heroicon-o-play-circle')
                    ->media(fn (UnitsAttachment $record) => asset('storage/' .$record->file_path)),
                EditAction::make()
                    ->modalHeading('Edit attachment')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['file_name'] = trim($data['file_name']);
                        $data['description'] = trim($data['description']);

                        return $data;
                    }),
                DeleteAction::make()
                    ->after(fn (UnitsAttachment $record): bool => Storage::disk('public')->delete($record->file_path))
                    ->modalHeading('Delete attachment?'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
