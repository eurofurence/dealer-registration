<?php

namespace App\Filament\Resources\ProfileResource\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;


class ApplicationRelationManager extends RelationManager
{
    protected static string $relationship = 'application';

    protected static ?string $title = "Application";
    protected static ?string $label = "application";

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
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
                Tables\Columns\TextColumn::make('requestedTable.name'),
                Tables\Columns\TextColumn::make('assignedTable.name'),
                Tables\Columns\TextColumn::make('table_number')
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
