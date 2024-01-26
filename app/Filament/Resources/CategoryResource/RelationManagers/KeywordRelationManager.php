<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Resources\KeywordResource;
use App\Models\Keyword;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;


class KeywordRelationManager extends RelationManager
{
    protected static string $relationship = 'keywords';

    protected static ?string $title = "Keywords";
    protected static ?string $label = "keywords";
    protected static ?string $recordTitleAttribute = "name";

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')->datetime(),
                Tables\Columns\TextColumn::make('updated_at')->datetime()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn (Keyword $record): string => KeywordResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
