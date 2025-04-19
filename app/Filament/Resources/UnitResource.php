<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ModuleCluster;
use App\Filament\Resources\UnitResource\Pages\CreateUnit;
use App\Filament\Resources\UnitResource\Pages\EditUnit;
use App\Filament\Resources\UnitResource\Pages\ListUnits;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Filament\Resources\UnitResource\RelationManagers\UnitsAttachmentRelationManager;
use App\Models\Module;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-book-open';
    protected static ?string $cluster = ModuleCluster::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('module_id')
                    ->relationship('module', 'name')
                    ->required(),
                TextInput::make('name')
                    ->maxLength(128)
                    ->minLength(5)
                    ->required()
                    ->unique(ignoreRecord: true),
                MarkdownEditor::make('description')
                    ->columnSpan(2)
                    ->maxLength(500)
                    ->minLength(5)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module.name')
                    ->label('Module')
                    ->searchable()
                    ->sortable()
                    ->words(5),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->words(5),
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable()
                    ->words(5),
                IconColumn::make('unit_attachments')
                    ->boolean() // Automatically handles true/false states
                    ->getStateUsing(fn ($record) => $record->unit_attachments()->exists())
                    ->label('Has Attachments'),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->options(Module::select(['id', 'name'])
                        ->get()
                        ->pluck('name', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('module',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    }),
                Filter::make('unit_attachments')
                    ->label('Show units with attachments')
                    ->query(fn (Builder $query) => $query->whereHas('unit_attachments'))
            ])
            ->defaultGroup('module.name')
            ->groups([
                Group::make('module.name')
                    ->label('Module'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UnitsAttachmentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }
}
