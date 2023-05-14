<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\StatusNotificationResult;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Http\Controllers\Applications\ApplicationController;
use App\Models\Application;
use App\Models\TableType;
use App\Notifications\AcceptedNotification;
use App\Notifications\OnHoldNotification;
use App\Notifications\WaitingListNotification;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
                            "table_offered" => "Table offered",
                            "table_accepted" => "Table accepted",
                            "checked_in" => "Checked in (Onsite)"
                        ])->required()->reactive(),
                        Forms\Components\TextInput::make('table_number')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_notified')->label('Notification sent'),
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
                Tables\Columns\TextColumn::make('user.name')->searchable(),
                Tables\Columns\BadgeColumn::make('status')->enum(ApplicationStatus::cases())->formatStateUsing(function (Application $record) {
                    return $record->status->name;
                })->colors([
                    'secondary',
                    'success' => ApplicationStatus::TableAccepted->value,
                    'danger' => ApplicationStatus::Canceled->value
                ]),
                Tables\Columns\TextColumn::make('requestedTable.name'),
                Tables\Columns\SelectColumn::make('table_type_assigned')->options(TableType::pluck('name', 'id')->toArray()),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(function (string $state) {
                    return ucfirst($state);
                })->sortable(),
                Tables\Columns\TextInputColumn::make('table_number')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('display_name')->searchable(),
                Tables\Columns\IconColumn::make('wanted_neighbors')->label('N Wanted')->default(false)->boolean(),
                Tables\Columns\IconColumn::make('comment')->default(false)->boolean(),
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
                Tables\Columns\IconColumn::make('is_notified')
                    ->label('Notification sent')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('parent')->query(fn (Builder $query): Builder => $query->where('type', 'dealer'))->label('Only Dealerships'),
                Tables\Filters\Filter::make('assignedTable')->query(fn (Builder $query): Builder => $query->whereNull('table_type_assigned'))->label('Missing assigned table'),
                Tables\Filters\Filter::make('table_number')->query(fn (Builder $query): Builder => $query->whereNull('table_number'))->label('Missing table number'),
                Tables\Filters\Filter::make('is_afterdark')->query(fn (Builder $query): Builder => $query->where('is_afterdark', '=', '1'))->label('Is Afterdark'),
                Tables\Filters\Filter::make('table_assigned')->query(fn (Builder $query): Builder => $query->whereNotNull('offer_sent_at'))->label('Table assigned'),
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
                            0);
                        foreach ($records as $record) {
                            $result = ApplicationController::sendStatusNotification($record);
                            $results[$result->name] += 1;
                        }

                        $statusCounts = '';

                        foreach ($results as $statusName=>$count) {
                            switch($statusName) {
                                case StatusNotificationResult::Accepted->name:
                                    $statusCounts .= "<li>{$count} notified about being accepted with requested table</li>";
                                    break;
                                case StatusNotificationResult::OnHold->name:
                                    $statusCounts .= "<li>{$count} notified about offered table differing from requested table (on-hold)</li>";
                                    break;
                                case StatusNotificationResult::WaitingList->name:
                                    $statusCounts .= "<li>{$count} notified about waiting list</li>";
                                    break;
                                case StatusNotificationResult::NotApplicable->name:
                                    $statusCounts .= "<li>{$count} not notified because application status was not applicable</li>";
                                    break;
                                case StatusNotificationResult::AlreadySent->name:
                                    $statusCounts .= "<li>{$count} not notified because notifications were already sent previously</li>";
                                    break;
                                default:
                                    $statusCounts .= "<li>{$count} with unknown status {$statusName}</li>";
                                    break;
                            }
                        }

                        Notification::make()
                            ->title("Bulk notifications sent")
                            ->body("<ul>
                            {$statusCounts}
                            </ul>")
                            ->success()->persistent()->send();
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-mail'),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            RelationManagers\ChildrenRelationManager::class,
            RelationManagers\ParentRelationManager::class
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
