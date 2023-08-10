<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\StatusNotificationResult;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Http\Controllers\Applications\ApplicationController;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\Application;
use App\Models\TableType;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $emailIcon = 'heroicon-o-envelope';
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()->columnSpan(2)->columns()->schema([
                    Forms\Components\TextInput::make('display_name')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('website')
                        ->maxLength(255),

                    Forms\Components\Grid::make()->columns()->schema([
                        Forms\Components\TextInput::make('merchandise')
                            ->maxLength(65535),
                        Forms\Components\Fieldset::make('Checks')->columnSpan(1)->columns(4)->schema([
                            Forms\Components\Toggle::make('is_afterdark')->label('Afterdark'),
                            Forms\Components\Toggle::make('is_power')->label('Power'),
                            Forms\Components\Toggle::make('is_wallseat')->label('Wallseat'),
                        ]),
                    ]),

                    Forms\Components\Grid::make()->columns()->schema([
                        Forms\Components\Textarea::make('wanted_neighbors')
                            ->label('Wanted Neighbors')
                            ->maxLength(65535),
                    ]),
                    Forms\Components\Textarea::make('comment')
                        ->columnSpanFull()
                        ->maxLength(65535),
                ]),

                Forms\Components\Group::make()->schema([

                    Forms\Components\Fieldset::make('Status')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Select::make('status')->options([
                            "canceled" => "Canceled",
                            "open" => "Open",
                            "waiting" => "Waiting",
                            "table_assigned" => "Table assigned (Open)",
                            "table_offered" => "Table offered",
                            "table_accepted" => "Table accepted",
                            "checked_in" => "Checked in (on-site)"
                        ])->disablePlaceholderSelection()->required()->reactive(),
                        Forms\Components\TextInput::make('table_number')
                            ->maxLength(255),
                    ]),

                    Forms\Components\Fieldset::make('Relationships')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Select::make('type')->options(ApplicationType::class)
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('user_id')->searchable()->relationship('user', 'name')
                            ->required(),
                        Forms\Components\Select::make('parent')->searchable()->options(Application::getEligibleParents()->pluck('name', 'id'))
                            ->hidden(fn (\Closure $get) => $get('type') === ApplicationType::Dealer->value)
                            ->required(fn (\Closure $get) => $get('type') !== ApplicationType::Dealer->value),
                        Forms\Components\Select::make('table_type_requested')->relationship('requestedTable', 'name')
                            ->hidden(fn (\Closure $get) => $get('type') !== ApplicationType::Dealer->value)
                            ->required(fn (\Closure $get) => $get('type') === ApplicationType::Dealer->value),
                        Forms\Components\Select::make('table_type_assigned')->relationship('assignedTable', 'name')
                            ->hidden(fn (\Closure $get) => $get('type') !== ApplicationType::Dealer->value)
                            ->nullable(fn (\Closure $get) => $get('status') !== ApplicationStatus::TableOffered->value),
                    ]),

                    Forms\Components\Fieldset::make('Dates')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Placeholder::make('offer_sent_at')->content(fn (?Application $record): string => $record?->offer_sent_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('offer_accepted_at')->content(fn (?Application $record): string => $record?->offer_accepted_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('waiting_at')->content(fn (?Application $record): string => $record?->waiting_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('checked_in_at')->content(fn (?Application $record): string => $record?->checked_in_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('canceled_at')->content(fn (?Application $record): string => $record?->canceled_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('updated_at')->content(fn (?Application $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('created_at')->content(fn (?Application $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label("ID"),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->enum(ApplicationStatus::cases())
                    ->formatStateUsing(function (Application $record) {
                        return $record->status->name;
                    })
                    ->colors([
                        'secondary',
                        'success' => ApplicationStatus::TableAccepted->value,
                        'danger' => ApplicationStatus::Canceled->value
                    ]),
                Tables\Columns\TextColumn::make('requestedTable.name')
                    ->icon(fn ($record) => $record->type === ApplicationType::Dealer && $record->table_type_requested !== $record->table_type_assigned ? 'heroicon-o-exclamation' : '')
                    ->iconPosition('after')
                    ->color(fn ($record) => $record->type === ApplicationType::Dealer && $record->table_type_requested !== $record->table_type_assigned ? 'warning' : '')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('tableTypeAssignedAutoNull')
                    ->label('Assigned table')
                    ->options(TableType::pluck('name', 'id')->toArray())
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('table_type_assigned', $direction);
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function (string $state) {
                        return ucfirst($state);
                    })
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('table_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_ready')
                    ->label('Ready')
                    ->getStateUsing(function (Application $record) {
                        return $record->isReady();
                    })
                    ->boolean(),
                Tables\Columns\TextColumn::make('dlrshp')
                    ->getStateUsing(function (Application $record) {
                        return $record->parent ?: $record->id;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy(DB::raw('IF(`type` = \'dealer\', `id`,`parent`)'), $direction);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('id', '=', $search)
                            ->orWhere('parent', '=', $search);
                    }),
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable(),
                // TODO fetch all regs at once rather than individual requests.
                // Tables\Columns\TextColumn::make('regstatus')
                //     ->getStateUsing(function (Application $record) {
                //         $reg = RegSysClientController::getSingleReg($record->user()->first()->reg_id);
                //         return  $reg != null ? $reg['status'] : "";
                //     }),
                Tables\Columns\IconColumn::make('wanted_neighbors')
                    ->label('N Wanted')
                    ->default(false)
                    ->boolean(),
                Tables\Columns\IconColumn::make('comment')
                    ->default(false)
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_afterdark')
                    ->label('AD')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_power')
                    ->label('Power')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_wallseat')
                    ->label('Wallseat')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('parent')
                    ->query(fn (Builder $query): Builder => $query->where('type', 'dealer'))
                    ->label('Only Dealerships'),
                Tables\Filters\Filter::make('assignedTable')
                    ->query(fn (Builder $query): Builder => $query->whereNull('table_type_assigned'))
                    ->label('Missing assigned table'),
                Tables\Filters\Filter::make('table_number')
                    ->query(fn (Builder $query): Builder => $query->whereNull('table_number'))
                    ->label('Missing table number'),
                Tables\Filters\Filter::make('is_afterdark')
                    ->query(fn (Builder $query): Builder => $query->where('is_afterdark', '=', '1'))
                    ->label('Is Afterdark'),
                Tables\Filters\Filter::make('table_assigned')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('offer_sent_at'))
                    ->label('Table assigned'),
                Tables\Filters\SelectFilter::make('requestedTable')
                    ->relationship('requestedTable', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('assignedTable')
                    ->relationship('assignedTable', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(array_column(ApplicationStatus::cases(), 'value'), array_column(ApplicationStatus::cases(), 'name')))
                    ->query(function (Builder $query, array $data) {
                        \Illuminate\Support\Facades\Log::debug(print_r($data, true));
                        if (!key_exists('values', $data)) {
                            return;
                        }
                        $query->where(function (Builder $query) use ($data) {
                            foreach ($data['values'] as $value) {
                                ApplicationStatus::tryFrom($value)?->orWhere($query);
                            }
                        });
                    })
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('Send status notification')
                    ->action(function (Collection $records): void {
                        $resultsType = StatusNotificationResult::class;
                        $results = array_fill_keys(
                            array_map(
                                fn (StatusNotificationResult $r) => $r->name,
                                $resultsType::cases()
                            ),
                            0
                        );
                        foreach ($records as $record) {
                            $result = ApplicationController::sendStatusNotification($record);
                            $results[$result->name] += 1;
                        }

                        $statusCounts = '';
                        $totalSentCount = 0;
                        $frontendNotification = Notification::make();

                        foreach ($results as $statusName => $count) {
                            switch ($statusName) {
                                case StatusNotificationResult::Accepted->name:
                                    $statusCounts .= "<li>{$count} notified about being accepted with requested table</li>";
                                    $totalSentCount += $count;
                                    break;
                                case StatusNotificationResult::OnHold->name:
                                    $statusCounts .= "<li>{$count} notified about offered table differing from requested table (on-hold)</li>";
                                    $totalSentCount += $count;
                                    break;
                                case StatusNotificationResult::WaitingList->name:
                                    $statusCounts .= "<li>{$count} notified about waiting list</li>";
                                    $totalSentCount += $count;
                                    break;
                                case StatusNotificationResult::SharesInvalid->name:
                                    $statusCounts .= "<li>{$count} not notified because shares/assistants not assigned to same table</li>";
                                    break;
                                case StatusNotificationResult::StatusNotApplicable->name:
                                    $statusCounts .= "<li>{$count} not notified because status not applicable</li>";
                                    break;
                                case StatusNotificationResult::NotDealer->name:
                                    $statusCounts .= "<li>{$count} not directly notified because share/assistant</li>";
                                    break;
                                default:
                                    $statusCounts .= "<li>{$count} with unknown status {$statusName}</li>";
                                    break;
                            }
                        }

                        if ($totalSentCount > 0) {
                            $frontendNotification->title("{$totalSentCount} bulk notifications sent")
                                ->success();
                        } else {
                            $frontendNotification->title("No bulk notifications sent")
                                ->warning();
                        }

                        $frontendNotification->body("<ul>
                            {$statusCounts}
                            </ul>")->persistent()->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-mail'),
                Tables\Actions\BulkAction::make('Send reminder')
                    ->action(function (Collection $records): void {
                        $resultsType = StatusNotificationResult::class;
                        $results = array_fill_keys(
                            array_map(
                                fn (StatusNotificationResult $r) => $r->name,
                                $resultsType::cases()
                            ),
                            0
                        );
                        foreach ($records as $record) {
                            $result = ApplicationController::sendReminderNotification($record);
                            $results[$result->name] += 1;
                        }

                        $statusCounts = '';
                        $totalSentCount = 0;
                        $frontendNotification = Notification::make();

                        foreach ($results as $statusName => $count) {
                            switch ($statusName) {
                                case StatusNotificationResult::Accepted->name:
                                    $statusCounts .= "<li>{$count} reminded about having to accept their table</li>";
                                    $totalSentCount += $count;
                                    break;
                                case StatusNotificationResult::StatusNotApplicable->name:
                                    $statusCounts .= "<li>{$count} not notified because status not applicable</li>";
                                    break;
                                case StatusNotificationResult::NotDealer->name:
                                    $statusCounts .= "<li>{$count} not directly notified because share/assistant</li>";
                                    break;
                                default:
                                    $statusCounts .= "<li>{$count} with unknown status {$statusName}</li>";
                                    break;
                            }
                        }

                        if ($totalSentCount > 0) {
                            $frontendNotification->title("{$totalSentCount} bulk notifications sent")
                                ->success();
                        } else {
                            $frontendNotification->title("No bulk notifications sent")
                                ->warning();
                        }

                        $frontendNotification->body("<ul>
                            {$statusCounts}
                            </ul>")->persistent()->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-mail'),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            RelationManagers\ChildrenRelationManager::class,
            RelationManagers\ParentRelationManager::class,
            RelationManagers\UserRelationManager::class,
            RelationManagers\ProfileRelationManager::class,
            RelationManagers\CommentRelationManager::class,
        ];
    }

    protected function shouldPersistTableFiltersInSession(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
