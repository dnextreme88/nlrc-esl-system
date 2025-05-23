<?php

namespace App\Filament\Resources;

use App\Events\ReceiveAnnouncementEvent;
use App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Filament\Resources\AnnouncementResource\Pages\ViewAnnouncement;
use App\Mail\AnnouncementEmail;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentKnowledgeBase\Contracts\HasKnowledgeBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource implements HasKnowledgeBase
{
    protected static ?string $model = Announcement::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-megaphone';
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function getDocumentation(): array
    {
        return ['announcements.intro'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('user_id')
                    ->content(fn (Announcement $record): string => $record->user->first_name)
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->label('Author'),
                TextInput::make('title')
                    ->columnSpan(['md' => 2])
                    ->maxLength(128)
                    ->minLength(5)
                    ->required()
                    ->unique(ignoreRecord: true),
                MarkdownEditor::make('description')
                    ->columnSpan(['md' => 2])
                    ->disableToolbarButtons(['attachFiles'])
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TagsInput::make('tags')
                    ->columnSpan(['md' => 2])
                    ->extraAttributes(['class' => 'lowercase'])
                    ->helperText('To add the tag, press the Enter, Tab, or comma (,) keys. Each tag is limited to 32 characters only.')
                    ->nestedRecursiveRules(['max:32', 'min:2'])
                    ->nullable()
                    ->reorderable()
                    ->separator()
                    ->splitKeys([','])
                    ->suggestions(function () {
                        $tags = Announcement::all()->pluck('tags')
                            ->reduce(function (?string $carry, ?string $item): ?string {
                                if ($item) {
                                    $carry .= $item. ',';
                                }

                                return $carry;
                            });

                        $concatenated_tags = Str::of($tags)->explode(',')
                            ->unique()
                            ->sort()
                            ->values();

                        return $concatenated_tags;
                    }),
                Placeholder::make('created_at')
                    ->content(fn (Announcement $announcement): string => $announcement->created_at->isoFormat('LLL'))
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->label('Announced on'),
                Placeholder::make('updated_at')
                    ->content(fn (Announcement $announcement): string => $announcement->updated_at->isoFormat('LLL'))
                    ->hidden(fn (string $operation): bool => $operation === 'create')
                    ->label('Updated on'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('user.first_name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags')
                    ->formatStateUsing(function (string $state) {
                        $tags = array_map('trim', explode(',', $state));
                        $badges = collect($tags)->map(
                            fn ($tag): string => "<span class='inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium mr-1 bg-transparent border-2 text-gray-800 dark:text-gray-200 border-green-300 dark:border-green-600'>{$tag}</span>"
                        )->implode(' ');

                        return new HtmlString($badges);
                    })
                    ->html()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->label('Announced on')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('M d, Y h:i:s A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label('Staff Name')
                    ->options(fn () => User::select(['id', 'first_name'])->whereRaw('role_id IN(1, 2, 4)')
                        ->get()
                        ->pluck('first_name', 'id')
                    )
                    ->query(function (Builder $query, array $data) {
                        // REF: https://v2.filamentphp.com/tricks/use-selectfilter-on-distant-relationships
                        if (!empty($data['value'])) {
                            return $query->whereHas('user',
                                fn (Builder $query) => $query->where('id', '=', (int) $data['value'])
                            );
                        }
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->color('info')
                    ->icon('heroicon-o-eye')
                    ->label('View'),
                Action::make('send_announcement')
                    ->action(function (Announcement $record, array $data) {
                        $selected_list = $data['send_to_list'];

                        $base_query = User::select(['users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email', 'users.is_active'])
                            ->where('users.is_active', true);

                        /*
                        if ($selected_list == 'users') {
                            $recipients_list = $base_query->clone()
                                ->whereIn('id', $data['users_list']);

                            $suffix = count($data['users_list']). ' user/s';
                        } else {
                        */
                            $notifications = Notification::select('notifiable_id')->where('type', 'announcement-sent')
                                ->where('notifiable_type', 'App\Models\User')
                                ->where('data->announcement_id', $record->id)
                                ->get()
                                ->toArray();
                            $user_ids = array_unique(array_column($notifications, 'notifiable_id'));

                            $recipients_list = $base_query->clone()
                                ->join('roles', 'roles.id', 'users.role_id')
                                ->whereIn('roles.id', $data['roles_list'])
                                ->whereNotIn('users.id', $user_ids);

                            $suffix = count($data['roles_list']). ' role/s';
                        /*
                        }
                        */

                        $recipients_list = $recipients_list->chunk(200, function (Collection $recipients) use ($record) {
                            LaravelNotification::send($recipients, new AnnouncementNotification($record));

                            foreach ($recipients as $user) {
                                Mail::to($user->email)->queue(new AnnouncementEmail($record, $user));

                                broadcast(new ReceiveAnnouncementEvent($user->id)); // Trigger an event
                            }
                        });

                        FilamentNotification::make()
                            ->body('Announcement was sent to ' .$suffix)
                            ->title('Announcement sent')
                            ->success()
                            ->send();
                    })
                    ->color('warning')
                    ->form([
                        Select::make('send_to_list')
                            ->label('Select Recipients')
                            ->live()
                            ->options([
                                // 'users' => 'Active Users',
                                'roles' => 'By Role',
                            ]),
                        /*
                        Select::make('users_list')
                            ->label('User Recipients')
                            ->placeholder('Select recipients')
                            ->multiple()
                            ->searchable()
                            ->live()
                            ->helperText('Only the first 20 users are loaded but you can still search for the recipients')
                            ->visible(fn (Get $get): bool => $get('send_to_list') == 'users')
                            ->optionsLimit(20)
                            ->options(fn (Announcement $record): Collection =>
                                User::selectRaw('id, CONCAT(last_name, ", ", first_name) AS name')
                                    ->isActive()
                                    ->whereNotIn('id', function (BuilderQuery $query) use ($record) {
                                        return $query->select(['notifiable_id'])
                                            ->from('notifications')
                                            ->where('type', 'announcement-sent')
                                            ->where('data->announcement_id', $record->id);
                                    })
                                    ->get()
                                    ->pluck('name', 'id')
                            ),
                        */
                        Select::make('roles_list')
                            ->label('Roles')
                            ->live()
                            ->multiple()
                            ->searchable()
                            ->options(fn (): Collection => Role::select(['id', 'name'])->pluck('name', 'id'))
                            ->placeholder('Select roles')
                            ->visible(fn (Get $get): bool => $get('send_to_list') == 'roles'),
                    ])
                    ->icon('heroicon-o-paper-airplane')
                    ->modalSubmitActionLabel('Send and notify'),
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Announcement Details')
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    InfolistGroup::make([
                                        TextEntry::make('user.first_name')
                                            ->label('Author'),
                                        TextEntry::make('title')
                                    ]),
                                    InfolistGroup::make([
                                        TextEntry::make('created_at')
                                            ->badge()
                                            ->color('success')
                                            ->date(),
                                        TextEntry::make('updated_at')
                                            ->badge()
                                            ->color('warning')
                                            ->date(),
                                    ]),
                                    TextEntry::make('description')
                                        ->columnSpanFull()
                                        ->markdown(),
                                ]),
                        ])
                    ]),
                InfolistSection::make('Author Details')
                    ->schema([
                        TextEntry::make('user.email')
                            ->default('N/A')
                            ->label('Email'),
                        TextEntry::make('user.role.name')
                            ->label('Role'),
                        TextEntry::make('user.timezone')
                            ->label('Timezone')
                    ])
                    ->columns(2)
            ]);
    }

    public static function getRecordSubNavigation($page): array
    {
        return $page->generateNavigationItems([
            EditAnnouncement::class,
            ViewAnnouncement::class
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
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
            'view' => ViewAnnouncement::route('/{record}'),
        ];
    }
}
