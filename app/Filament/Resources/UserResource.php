<?php

namespace App\Filament\Resources;

use App\Enums\Genders;
use App\Enums\Roles;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use DateTimeZone;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-user';
    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('User Details')
                            ->schema([
                                TextInput::make('first_name')
                                    ->maxLength(96)
                                    ->minLength(2)
                                    ->required(),
                                TextInput::make('middle_name')
                                    ->maxLength(96)
                                    ->minLength(2)
                                    ->nullable(),
                                TextInput::make('last_name')
                                    ->maxLength(96)
                                    ->minLength(2)
                                    ->required(),
                                TextInput::make('email')
                                    ->maxLength(96)
                                    ->minLength(2)
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('password')
                                    ->hidden(fn (string $operation): bool => $operation === 'edit')
                                    ->password()
                                    ->revealable(),
                                DatePicker::make('date_of_birth')
                                    ->required(),
                                Select::make('gender')
                                    ->options(Genders::class)
                                    ->required(),
                                Select::make('role_id')
                                    ->relationship('role', 'name', fn ($query) => $query->where('name', '!=', Roles::STUDENT->value))
                                    ->required(),
                                Select::make('timezone')
                                    ->options(function () {
                                        $timezone_names = DateTimeZone::listIdentifiers(timezoneGroup: DateTimeZone::ALL);
                                        $timezones_list = array_combine(
                                            $timezone_names,
                                            array_map(fn ($tz) => (new CarbonTimeZone($tz))->toOffsetName(), $timezone_names)
                                        );

                                        return collect($timezones_list)
                                            ->mapWithKeys(fn ($utc_offset, $timezone) => [$timezone => $timezone. ' (' .$utc_offset. ')'])
                                            ->toArray();
                                    })
                                    ->required(),
                            ])
                            ->columns(2)
                            ->hidden(fn (string $operation): bool => $operation === 'edit'),
                        Tab::make('Editable User Details')
                            ->schema([
                                Placeholder::make('first_name')
                                    ->content(fn (User $user): ?string => $user->first_name),
                                Placeholder::make('middle_name')
                                    ->content(fn (User $user): ?string => $user->middle_name),
                                Placeholder::make('last_name')
                                    ->content(fn (User $user): ?string => $user->last_name),
                                Placeholder::make('email')
                                    ->content(fn (User $user): ?string => $user->email),
                                Placeholder::make('date_of_birth')
                                    ->content(fn (User $user): ?string => Carbon::parse($user->date_of_birth)->format('m/d/Y')),
                                Placeholder::make('gender')
                                    ->content(fn (User $user): ?string => ucfirst($user->gender)),
                                Placeholder::make('role_id')
                                    ->content(fn (User $user): ?string => $user->role->name)
                                    ->label('Role')
                                    ->visible(fn (User $record): bool => $record->role->name == Roles::STUDENT->value),
                                Select::make('role_id')
                                    ->hidden(fn (User $record): bool => $record->role->name == Roles::STUDENT->value)
                                    ->relationship('role', 'name', fn ($query) => $query->where('name', '!=', Roles::STUDENT->value))
                                    ->required(),
                                Select::make('timezone')
                                    ->options(function () {
                                        $timezone_names = DateTimeZone::listIdentifiers(timezoneGroup: DateTimeZone::ALL);
                                        $timezones_list = array_combine(
                                            $timezone_names,
                                            array_map(fn ($tz) => (new CarbonTimeZone($tz))->toOffsetName(), $timezone_names)
                                        );

                                        return collect($timezones_list)
                                            ->mapWithKeys(fn ($utc_offset, $timezone) => [$timezone => $timezone. ' (' .$utc_offset. ')'])
                                            ->toArray();
                                    })
                                    ->required(),
                                ToggleButtons::make('is_active')
                                    ->boolean()
                                    ->grouped()
                                    ->label('Is user active?')
                                    ->helperText('Choose no if the user is not actively using the web app'),
                            ])
                            ->columns(2)
                            ->hidden(fn (string $operation): bool => $operation === 'create')
                    ])
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->searchable(['first_name', 'middle_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->badge()
                    ->label('Role'),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')
                    ->badge()
                    ->color(fn (string $state): string => Genders::from($state)->getColor())
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('timezone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('role', 'name'),
                SelectFilter::make('gender')
                    ->options(fn () => collect(Genders::cases())
                        ->mapWithKeys(fn ($gender) => [$gender->value => $gender->getLabel()])
                        ->toArray()
                    ),
                Filter::make('is_active')
                    ->label('Toggle active users')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
