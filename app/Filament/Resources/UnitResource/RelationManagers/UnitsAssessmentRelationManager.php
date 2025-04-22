<?php

namespace App\Filament\Resources\UnitResource\RelationManagers;

use App\Enums\AssessmentTypes;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
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
    protected static string $relationship = 'assessments';
    protected static ?string $title = 'Assessments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('assessment_id')
                    ->helperText('Only active assessments will be shown here')
                    ->relationship(
                        name: 'assessment',
                        titleAttribute: 'title',
                        modifyQueryUsing: function ($query) {
                            $used_assessment_ids = $this->getOwnerRecord()
                                ->assessments() // Relationship name from Unit to Assessment
                                ->pluck('id')
                                ->toArray();

                            return $query->where('is_active', true)
                                ->whereNotIn('id', $used_assessment_ids);
                        },
                    )
                    ->required(),
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
                    ->label('Is assessment still active')
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-x-circle',
                        '1' => 'heroicon-s-check-circle',
                    }),
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
                            ->assessments() // Relationship name from Unit to Assessment
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
