<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';
    protected static ?string $title = 'Included Units';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // For now, creating of units are done on their own admin panel rather than this relation manager
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->words(5),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->words(5),
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable()
                    ->words(5),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->button()
                    ->icon('heroicon-m-book-open')
                    ->label('Add units')
                    ->url(fn () => route('filament.admin.module.resources.units.create', [
                        'module_id' => $this->getOwnerRecord()->id, // Use as id for selecting the module as default on the create units page
                    ])),
            ])
            ->emptyStateDescription('You may create units on their own admin panel by clicking the button below')
            ->emptyStateHeading('No units found for this module')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
