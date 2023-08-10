<?php

namespace App\Filament\Resources\CommentResource\RelationManagers;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;


class ApplicationRelationManager extends RelationManager
{
    protected static string $relationship = 'application';

    protected static ?string $title = "Application";
    protected static ?string $label = "application";

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\BadgeColumn::make('status')->enum(ApplicationStatus::cases())->formatStateUsing(function (Application $record) {
                    return $record->status->name;
                })->colors([
                        'secondary',
                        'success' => ApplicationStatus::TableAccepted->value,
                        'danger' => ApplicationStatus::Canceled->value
                    ]),
                Tables\Columns\TextColumn::make('assignedTable.name'),
                Tables\Columns\TextColumn::make('table_number'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(Application $record): string => ApplicationResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([
            ]);
    }
}
