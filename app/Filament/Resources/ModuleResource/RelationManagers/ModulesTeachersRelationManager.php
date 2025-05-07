<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use App\Enums\Roles;
use App\Models\ModulesTeacher;
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

class ModulesTeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'module_teachers';
    protected static ?string $title = 'Enrolled Teachers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('teacher_id')
                    ->columnSpanFull()
                    ->helperText('Only active teachers not enrolled on this module will be shown here')
                    ->label('Teacher')
                    ->options(function () {
                        $module_id = $this->getOwnerRecord()->id;

                        $query = User::query();

                        $used_teacher_ids = ModulesTeacher::isModuleId($module_id)->pluck('teacher_id')
                            ->toArray();

                        $query->where('is_active', true)
                            ->whereNotIn('id', $used_teacher_ids)
                            ->whereHas('role', fn ($query) =>
                                $query->where('name', Roles::HEAD_TEACHER->value)
                                    ->orWhere('name', Roles::TEACHER->value)
                            )
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
                TextColumn::make('teacher.full_name')
                    ->label('Teacher')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('teacher.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.timezone')
                    ->label('Timezone')
                    ->searchable(),
                IconColumn::make('teacher.is_active')
                    ->boolean()
                    ->label('Is active'),
            ])
            ->description('List of teachers who can view this module, access units, and take assessments under this module.')
            ->emptyStateDescription('Add a new teacher by clicking the top-right button')
            ->emptyStateHeading('No teachers found for this module')
            ->filters([
                Filter::make('is_active')
                    ->label('Toggle active users')
                    ->query(fn (Builder $query): Builder => $query->whereHas('teacher', fn ($query) => $query->where('is_active', true))),
            ])
            ->headerActions([
                CreateAction::make()
                    ->createAnother(false)
                    ->label('Enroll new teacher')
                    ->modalHeading('Enroll new teacher'),
            ])
            ->actions([
                DeleteAction::make('unenroll_teacher')
                    ->icon('heroicon-m-user-minus')
                    ->label('Unenroll')
                    ->modalDescription('Are you sure you would like to remove this teacher? He/She will not be able to view the units and take assessments under this module')
                    ->modalHeading('Unenroll teacher?')
                    ->modalIcon('heroicon-m-user-minus'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
