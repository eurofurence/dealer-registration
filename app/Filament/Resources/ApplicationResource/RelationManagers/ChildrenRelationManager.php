<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ChildrenRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    protected static ?string $title = "Shares and Assistants";
    protected static ?string $label = "application";
    protected static ?string $recordTitleAttribute = 'user.name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(function (string $state) {
                    return ucfirst($state);
                })->sortable(),
                Tables\Columns\BadgeColumn::make('status')->enum(ApplicationStatus::cases())->formatStateUsing(function (Application $record) {
                    return $record->status->name;
                })->colors([
                        'secondary',
                        'success' => ApplicationStatus::TableAccepted->value,
                        'danger' => ApplicationStatus::Canceled->value
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(Application $record): string => ApplicationResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\Action::make('Delete')
                    ->action(function (Application $record): void {
                        $record->setStatusAttribute(ApplicationStatus::Canceled);
                    })
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->bulkActions([
            ]);
    }
}
