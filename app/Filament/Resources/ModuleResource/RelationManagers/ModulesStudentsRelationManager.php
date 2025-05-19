<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use App\Enums\Roles;
use App\Models\ModulesStudent;
use App\Models\User;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ModulesStudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'module_students';
    protected static ?string $title = 'Enrolled Students';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->columnSpanFull()
                    ->helperText('Only active students not enrolled on this module will be shown here')
                    ->label('Student')
                    ->options(function () {
                        $module_id = $this->getOwnerRecord()->id;

                        $query = User::query();

                        $used_student_ids = ModulesStudent::isModuleId($module_id)->pluck('student_id')
                            ->toArray();

                        $query->where('is_active', true)
                            ->whereNotIn('id', $used_student_ids)
                            ->whereHas('role', fn ($query) => $query->where('name', Roles::STUDENT->value))
                            ->orderBy('last_name')
                            ->orderBy('first_name')
                            ->orderBy('middle_name');

                        return $query->get()
                            ->mapWithKeys(fn ($user) => [$user->id => $user->full_name])
                            ->toArray();
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('student.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.timezone')
                    ->label('Timezone')
                    ->searchable(),
                IconColumn::make('student.is_active')
                    ->boolean()
                    ->label('Is active'),
            ])
            ->description('List of students who can view this module, access units, and take assessments under this module.')
            ->emptyStateDescription('Add a new student by clicking the top-right button')
            ->emptyStateHeading('No students found for this module')
            ->filters([
                Filter::make('is_active')
                    ->label('Toggle active users')
                    ->query(fn (Builder $query): Builder => $query->whereHas('student', fn ($query) => $query->where('is_active', true))),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->label('Enroll new student')
                    ->modalHeading('Enroll new student'),
            ])
            ->actions([
                DeleteAction::make('unenroll_student')
                    ->icon('heroicon-m-user-minus')
                    ->label('Unenroll')
                    ->modalDescription('Are you sure you would like to remove this student? He/She will not be able to view the units and take assessments under this module')
                    ->modalHeading('Unenroll student?')
                    ->modalIcon('heroicon-m-user-minus'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
