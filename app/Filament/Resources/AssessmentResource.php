<?php

namespace App\Filament\Resources;

use App\Enums\AssessmentTypes;
use App\Filament\Clusters\AssessmentCluster;
use App\Filament\Resources\AssessmentResource\Pages\CreateAssessment;
use App\Filament\Resources\AssessmentResource\Pages\EditAssessment;
use App\Filament\Resources\AssessmentResource\Pages\ListAssessments;
use App\Filament\Resources\AssessmentResource\RelationManagers;
use App\Filament\Resources\AssessmentResource\RelationManagers\AssessmentsQuestionRelationManager;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentResource extends Resource implements HasKnowledgeBase
{
    protected static ?string $model = Assessment::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil-square';
    protected static ?string $cluster = AssessmentCluster::class;
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function getDocumentation(): array
    {
        return ['assessments.intro'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->maxLength(128)
                    ->minLength(5)
                    ->required(),
                Select::make('type')
                    ->options(AssessmentTypes::class)
                    ->required(),
                MarkdownEditor::make('description')
                    ->columnSpan(['md' => 2])
                    ->minLength(5)
                    ->required(),
                Toggle::make('is_active')
                    ->columnSpan(['md' => 2])
                    ->default(0)
                    ->helperText('Determines whether this assessment can be taken by students'),
                Placeholder::make('created_at')
                    ->content(fn (Assessment $assessment): string => $assessment->created_at->isoFormat('LLL'))
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->label('Created on'),
                Placeholder::make('updated_at')
                    ->content(fn (Assessment $assessment): string => $assessment->updated_at->isoFormat('LLL'))
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->label('Updated on'),
            ])
            ->columns(['md' => 2]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AssessmentTypes::from($state)->getLabel()),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('questionsCount')
                    ->label('# of questions'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(fn () => collect(AssessmentTypes::cases())
                        ->mapWithKeys(fn ($assessment_type) => [$assessment_type->value => $assessment_type->getLabel()])
                        ->toArray()
                    ),
                Filter::make('is_active')
                    ->label('Toggle active assessments')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (Assessment $record) => $record->questionsCount == 0),
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
            AssessmentsQuestionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssessments::route('/'),
            'create' => CreateAssessment::route('/create'),
            'edit' => EditAssessment::route('/{record}/edit'),
        ];
    }
}
