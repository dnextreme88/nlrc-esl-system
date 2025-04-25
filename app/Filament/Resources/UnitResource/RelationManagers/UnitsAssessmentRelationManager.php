<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use App\Enums\AssessmentTypes;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitsAssessmentRelationManager extends RelationManager
{
    protected static string $relationship = 'unit_assessments';
    protected static ?string $title = 'Assessments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('assessment_id')
                    ->helperText('Only active assessments with at least 1 question will be shown here')
                    ->live()
                    ->relationship(
                        name: 'assessment',
                        titleAttribute: 'title',
                        modifyQueryUsing: function ($query) {
                            $used_assessment_ids = $this->getOwnerRecord()
                                ->unit_assessments() // Relationship name of Unit to Assessment
                                ->pluck('assessment_id')
                                ->toArray();

                            return $query->where('is_active', true)
                                ->whereHas('questions')
                                ->whereNotIn('id', $used_assessment_ids);
                        },
                    )
                    ->required(),
                Placeholder::make('no_of_questions')
                    ->content(function (Get $get): int {
                        $assessment_questions_count = Assessment::find($get('assessment_id'))->questions->count();

                        return $assessment_questions_count;
                    })
                    ->label('No. of questions for this assessment')
                    ->live()
                    ->visible(fn (Get $get): bool => filled($get('assessment_id'))),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assessment.title')
                    ->label('Assessment Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assessment.type')
                    ->formatStateUsing(fn (string $state): string => AssessmentTypes::from($state)->getLabel())
                    ->label('Assessment Type')
                    ->sortable(),
                IconColumn::make('assessment.is_active')
                    ->boolean()
                    ->label('Is assessment still active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Attached on')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Updated at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->description('Assessments that can be taken by the students who has access to the module and unit. Only active assessments will be shown to the students.')
            ->emptyStateDescription('Add a new assessment by clicking the top-right button')
            ->emptyStateHeading('No assessments found for this unit')
            ->filters([
                SelectFilter::make('type')
                    ->label('Assessment Type')
                    ->options(collect(AssessmentTypes::cases())
                        ->mapWithKeys(fn ($assessment_type) => [$assessment_type->value => $assessment_type->getLabel()])
                        ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('assessment',
                                fn (Builder $query) => $query->where('type', '=', $data['value'])
                            );
                        }
                    }),
                Filter::make('is_active')
                    ->label('Toggle active assessments')
                    ->query(fn (Builder $query): Builder => $query->whereHas('assessment',
                        fn (Builder $query) => $query->where('is_active', true)
                    )),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->hidden(function (): bool {
                        $used_assessment_ids = $this->getOwnerRecord()
                            ->unit_assessments() // Relationship name of Unit to Assessment
                            ->pluck('id')
                            ->toArray();

                        $available_assessments_left = Assessment::where('is_active', true)->whereNotIn('id', $used_assessment_ids)
                            ->count();

                        return $available_assessments_left === 0;
                    })
                    ->label('Add new assessment')
                    ->modalHeading('Add new assessment')
                    ->modalSubmitActionLabel('Attach assessment'),
            ])
            ->actions([
                DeleteAction::make()
                    ->modalHeading('Delete assessment?'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
