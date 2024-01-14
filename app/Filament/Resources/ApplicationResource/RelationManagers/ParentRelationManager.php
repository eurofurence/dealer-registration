<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class ParentRelationManager extends RelationManager
{
    protected static string $relationship = 'parent';

    protected static ?string $title = "Parent";
    protected static ?string $label = "application";
    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(function (ApplicationType $state) {
                    return ucfirst($state->name);
                })->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function (Application $record) {
                        return $record->status->name;
                    })
                    ->color(fn (ApplicationStatus $state): string => match ($state) {
                        ApplicationStatus::TableAccepted->value => 'success',
                        ApplicationStatus::Canceled->value => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn (Application $record): string => ApplicationResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([]);
    }
}
