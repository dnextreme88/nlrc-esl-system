<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ModuleCluster;
use App\Filament\Resources\ModuleResource\Pages\CreateModule;
use App\Filament\Resources\ModuleResource\Pages\EditModule;
use App\Filament\Resources\ModuleResource\Pages\ListModules;
use App\Filament\Resources\ModuleResource\RelationManagers;
use App\Filament\Resources\ModuleResource\RelationManagers\UnitsRelationManager;
use App\Models\Module;
use App\Models\Proficiency;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-document-text';
    protected static ?string $cluster = ModuleCluster::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('proficiency_id')
                    ->relationship('proficiency', 'name')
                    ->required(),
                TextInput::make('name')
                    ->maxLength(128)
                    ->minLength(5)
                    ->required()
                    ->unique(ignoreRecord: true),
                MarkdownEditor::make('description')
                    ->columnSpan(2)
                    ->minLength(5)
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('proficiency.level_code')
                    ->label('Proficiency Code')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Module')
                    ->searchable()
                    ->sortable()
                    ->words(5),
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable()
                    ->words(5),
                TextColumn::make('unitsCount')
                    ->label('# of units'),
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
                SelectFilter::make('proficiency')
                    ->options(Proficiency::select(['id', 'level_code'])
                        ->get()
                        ->pluck('level_code', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('proficiency',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    }),
            ])
            ->defaultGroup('proficiency.name')
            ->groups([
                Group::make('proficiency.name')
                    ->label('Proficiency'),
            ])
            ->actions([
                EditAction::make(),
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
            UnitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
            'create' => CreateModule::route('/create'),
            'edit' => EditModule::route('/{record}/edit'),
        ];
    }
}
