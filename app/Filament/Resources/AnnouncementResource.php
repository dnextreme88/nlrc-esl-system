<?php

namespace App\Filament\Resources;

use App\Events\ReceiveAnnouncementEvent;
use App\Filament\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\EditAnnouncement;
use App\Filament\Resources\AnnouncementResource\Pages\ListAnnouncements;
use App\Filament\Resources\AnnouncementResource\Pages\ViewAnnouncement;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Mail\SendAnnouncementEmail;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as InfolistGroup;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder AS BuilderQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification as LaravelNotification;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;
    protected static ?string $activeNavigationIcon = 'heroicon-s-megaphone';
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Announcement Information')
                            ->schema([
                                Placeholder::make('user_id')
                                    ->columnSpan(2)
                                    ->content(fn (Announcement $record): string => $record->user->first_name)
                                    ->hidden(fn (string $operation): bool => $operation === 'create')
                                    ->label('Author'),
                                TextInput::make('title')
                                    ->columnSpan(2)
                                    ->maxLength(128)
                                    ->minLength(5)
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                MarkdownEditor::make('description')
                                    ->columnSpan(4)
                                    ->disableToolbarButtons(['attachFiles'])
                                    ->required(fn (string $operation): bool => $operation === 'create'),
                                TagsInput::make('tags')
                                    ->columnSpan(4)
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
                            ])
                            ->columns(4),
                        Tab::make('Send Announcement')
                            ->schema([
                                Select::make('send_to_list')
                                    ->label('Select Recipients')
                                    ->live()
                                    ->options([
                                        // 'users' => 'Active Users',
                                        'roles' => 'By Role'
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
                                Actions::make([
                                    Action::make('send_announcement')
                                        ->color('success')
                                        ->icon('heroicon-m-paper-airplane')
                                        ->visible(fn (Get $get): bool =>
                                            // $get('send_to_list') && (count($get('users_list')) > 0 || count($get('roles_list')) > 0)
                                            $get('send_to_list') && count($get('roles_list')) > 0
                                        )
                                        ->action(function (Get $get, Set $set, Announcement $record) {
                                            $selected_list = $get('send_to_list');

                                            $base_query = User::select(['users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email', 'users.is_active'])
                                                ->where('users.is_active', true);

                                            /*
                                            if ($selected_list == 'users') {
                                                $recipients_list = $base_query->clone()
                                                    ->whereIn('id', $get('users_list'));

                                                $suffix = count($get('users_list')). ' user/s';
                                            } else {
                                            */
                                                $notifications = Notification::select('notifiable_id')
                                                    ->where('type', 'announcement-sent')
                                                    ->where('notifiable_type', 'App\Models\User')
                                                    ->where('data->announcement_id', $record->id)
                                                    ->get()
                                                    ->toArray();
                                                $user_ids = array_unique(array_column($notifications, 'notifiable_id'));

                                                $recipients_list = $base_query->clone()
                                                    ->join('roles', 'roles.id', 'users.role_id')
                                                    ->whereIn('roles.id', $get('roles_list'))
                                                    ->whereNotIn('users.id', $user_ids);

                                                $suffix = count($get('roles_list')). ' role/s';
                                            /*
                                            }
                                            */

                                            $recipients_list = $recipients_list
                                                ->chunk(200, function (Collection $recipients) use ($record) {
                                                    LaravelNotification::send($recipients, new AnnouncementNotification($record));

                                                    foreach ($recipients as $user) {
                                                        // TODO: TO CREATE CLASS
                                                        /*
                                                        Mail::to($user->email)->queue(new SendAnnouncementEmail($record, $user));
                                                        Mail::to($user->email)->send(new SendAnnouncementEmail($record, $user));
                                                        */

                                                        broadcast(new ReceiveAnnouncementEvent($user->id)); // Trigger an event
                                                    }
                                                });

                                            /*
                                            $set('users_list', []);
                                            */
                                            $set('roles_list', []);

                                            FilamentNotification::make()
                                                ->body('Announcement was sent to ' .$suffix)
                                                ->title('Announcement sent')
                                                ->success()
                                                ->send();
                                        })
                                ])
                            ])
                            ->hidden(fn (string $operation): bool => $operation === 'create')
                    ])
            ])
            ->columns(1);
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
                TextColumn::make('description')
                    ->markdown()
                    ->words(5),
                TextColumn::make('tags')
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
                    ->options(User::select(['id', 'first_name'])
                        ->whereRaw('role_id IN(1, 2, 4)')
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
                    ->color('warning')
                    ->icon('heroicon-o-eye')
                    ->label('View'),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle('Announcement deleted'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
