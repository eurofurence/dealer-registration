<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use App\Notifications\AcceptedNotification;
use App\Notifications\OnHoldNotification;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
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
                            ->label('Wanted')
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
                        ])->required(),
                        Forms\Components\TextInput::make('table_number')
                            ->maxLength(255),
                    ]),

                    Forms\Components\Fieldset::make('Relationships')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Select::make('type')->options(ApplicationType::class)
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('user_id')->searchable()->relationship('user', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('parent')
                            ->maxLength(255)
                            ->helperText("Enter the parent's application ID or submit an empty field to delete the parent relation"),
                        Forms\Components\Select::make('table_type_requested')->relationship('requestedTable', 'name')
                            ->hidden(fn (\Closure $get) => $get('type') !== ApplicationType::Dealer->value)
                            ->required(fn (\Closure $get) => $get('type') === ApplicationType::Dealer->value),
                        Forms\Components\Select::make('table_type_assigned')->relationship('assignedTable', 'name')
                            ->hidden(fn (\Closure $get) => $get('type') !== ApplicationType::Dealer->value)
                            ->nullable(),
                    ]),

                    Forms\Components\Fieldset::make('Dates')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Placeholder::make('offer_sent_at')->content(fn(?Application $record): string => $record?->offer_sent_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('offer_accepted_at')->content(fn(?Application $record): string => $record?->offer_accepted_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('waiting_at')->content(fn(?Application $record): string => $record?->waiting_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('checked_in_at')->content(fn(?Application $record): string => $record?->checked_in_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('canceled_at')->content(fn(?Application $record): string => $record?->canceled_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('updated_at')->content(fn(?Application $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                        Forms\Components\Placeholder::make('created_at')->content(fn(?Application $record): string => $record?->created_at?->diffForHumans() ?? '-'),
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
                Tables\Columns\TextColumn::make('assignedTable.name'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(function (string $state) {
                    return ucfirst($state);
                })->sortable(),
                Tables\Columns\TextColumn::make('display_name')->searchable(),
                Tables\Columns\TextInputColumn::make('table_number')->sortable()->searchable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('parent')->query(fn (Builder $query): Builder => $query->where('type','dealer'))->label('Only Dealerships'),
                Tables\Filters\Filter::make('assignedTable')->query(fn (Builder $query): Builder => $query->whereNull('table_type_assigned'))->label('Missing assigned table'),
                Tables\Filters\Filter::make('table_number')->query(fn (Builder $query): Builder => $query->whereNull('table_number'))->label('Missing table number'),
                Tables\Filters\Filter::make('is_afterdark')->query(fn (Builder $query): Builder => $query->where('is_afterdark','=','1'))->label('Is Afterdark'),
                Tables\Filters\Filter::make('table_assigned')->query(fn (Builder $query): Builder => $query->whereNotNull('offer_sent_at'))->label('Table assigned'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('Send table offer')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $tableData = $record->assignedTable()->first()->name . ' - ' . $record->assignedTable()->first()->price/100 . ' EUR';

                            if ($record->assignedTable()->first()->name === $record->requestedTable()->first()->name) {
                                $record->user()->first()->notify(new AcceptedNotification($tableData));
                            } else {
                                $record->user()->first()->notify(new OnHoldNotification($tableData));
                            }
                        }
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChildrenRelationManager::class
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
