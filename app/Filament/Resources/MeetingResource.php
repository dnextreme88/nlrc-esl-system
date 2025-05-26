<?php

namespace App\Filament\Resources;

use App\Enums\MeetingStatuses;
use App\Filament\Resources\MeetingResource\Pages;
use App\Filament\Resources\MeetingResource\Pages\ListMeetings;
use App\Filament\Resources\MeetingResource\RelationManagers;
use App\Helpers\Helpers;
use App\Models\Meetings\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-calendar';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.full_name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('meeting_uuid')
                    ->copyable()
                    ->icon('heroicon-o-clipboard')
                    ->label('UUID')
                    ->searchable(),
                TextColumn::make('meeting_date')
                    ->date('F j, Y')
                    ->sortable(),
                TextColumn::make('duration'),
                TextColumn::make('meeting_link')
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString("<a href='{$state}' class='underline hover:no-underline'>{$state}</a>"))
                    ->html()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('studentsCount')
                    ->label('Booked students')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_opened')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => MeetingStatuses::from($state)->getColor()),
            ])
            ->defaultSort(fn (Builder $query): Builder => $query
                ->orderBy('meeting_date')
                ->orderBy('start_time')
            )
            ->filters([
                SelectFilter::make('teacher')
                    ->options(fn () => User::select(['id', 'first_name'])->whereRaw('role_id IN(1, 2, 4)')
                        ->get()
                        ->pluck('first_name', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('teacher',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    }),
                SelectFilter::make('status')
                    ->options(fn () => collect(MeetingStatuses::cases())
                        ->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()])
                        ->toArray()
                    ),
                Filter::make('show_meetings_today')
                    ->query(fn (Builder $query) => $query->where('meeting_date', Helpers::parse_time_to_user_timezone(Carbon::today())->format('Y-m-d'))),
                Filter::make('show_meetings_with_links')
                    ->query(fn (Builder $query) => $query->whereNotNull('meeting_link')),
                Filter::make('show_meetings_with_notes')
                    ->query(fn (Builder $query) => $query->whereNotNull('notes')),
                Filter::make('show_meetings_that_cannot_be_booked')
                    ->query(fn (Builder $query) => $query->where('is_opened', 0)),
            ])
            ->actions([
                //
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
            'index' => ListMeetings::route('/'),
        ];
    }
}
