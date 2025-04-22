<?php

namespace App\Filament\Resources\AssessmentResource\RelationManagers;

use App\Models\AssessmentsQuestion;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentsQuestionRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';
    protected static ?string $title = 'Assessment Questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('question')
                    ->columnSpanFull()
                    ->label('Title')
                    ->minLength(5)
                    ->required(),
                Repeater::make('choices')
                    ->addActionLabel('Add choice')
                    ->columnSpanFull()
                    ->defaultItems(2)
                    ->maxItems(8)
                    ->relationship()
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            $number_of_choices = count($value);
                            $array_as_collection = collect($value);
                            if (!is_array($value) || $number_of_choices < 2) {
                                $fail('This question must have at least two choices.');
                            }

                            $has_at_least_one_correct_choice = $array_as_collection->contains(fn ($choice): bool => $choice['is_correct'] ?? false);
                            $has_all_correct_choices = $array_as_collection->every(fn ($choice) => $choice['is_correct'] == true);

                            if (!$has_at_least_one_correct_choice) {
                                $fail('This question must have at least one correct answer.');
                            } else if ($has_at_least_one_correct_choice && $has_all_correct_choices == $number_of_choices) {
                                $fail('This question cannot have all choices as correct answers.');
                            }
                        },
                    ])
                    ->schema([
                        TextInput::make('choice')
                            ->label('Content')
                            ->required(),
                        ToggleButtons::make('is_correct')
                            ->boolean()
                            ->default(0)
                            ->grouped()
                            ->label('Is correct answer?'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('choicesCount')
                    ->label('# of choices'),
                TextColumn::make('choicesAnswersCount')
                    ->label('# of correct answers'),
            ])
            ->emptyStateDescription('Add at least 1 question for this assessment by clicking the top-right button')
            ->emptyStateHeading('No questions found for this assessment')
            ->filters([
                Filter::make('questions_more_than_1_answer')
                    ->label('Toggle questions with more than 1 answer')
                    ->query(fn (Builder $query) =>
                        $query->whereHas('choices', function ($q) {
                            $q->where('is_correct', true);
                        }, '>=', 2)
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add question')
                    ->modalHeading('Add question'),
            ])
            ->actions([
                Action::make('view_answer_keys')
                    ->action(fn () => null) // Just a modal for informative purposes
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn ($record) =>
                        view('filament.assessment-answer-keys', [
                            'assessment_question' => $record,
                            'assessment_choices' => $record->choices()->get(),
                        ])
                    )
                    ->modalHeading('Answer keys')
                    ->modalSubmitAction(false),
                EditAction::make(),
                DeleteAction::make()
                    ->before(fn (AssessmentsQuestion $record) => $record->choices()->delete())
                    ->modalDescription('Are you sure you would like to delete this question? The choices will also be deleted')
                    ->modalHeading('Delete question and choices?'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(fn (Collection $records) => $records->each(
                            fn ($record) => $record->choices()->delete()
                        ))
                        ->modalDescription('Are you sure you would like to delete these questions? The choices will also be deleted')
                        ->modalHeading('Delete questions and their choices?'),
                ]),
            ]);
    }
}
