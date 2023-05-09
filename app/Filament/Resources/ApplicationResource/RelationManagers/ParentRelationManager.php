<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Http\Controllers\Applications\ApplicationController;
use App\Models\Application;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ParentRelationManager extends RelationManager
{
    protected static string $relationship = 'parent';

    protected static ?string $title = "Parent";
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
                // Tables\Actions\Action::make('Add')
                //     ->action(function (Application $record, array $data): void {
                //         $record->parent = $data['id'];
                //         $record->update();
                //     })
                //     ->form([
                //         Forms\Components\Select::make('parent')
                //             // ->label('Parent')
                //             ->options(Application::getEligibleParents())
                //     ])
                //     ->requiresConfirmation()
                //     ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(Application $record): string => ApplicationResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([
            ]);
    }
}
