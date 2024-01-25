<?php

namespace App\Filament\Resources\KeywordResource\RelationManagers;

use App\Filament\Resources\ProfileResource;
use App\Models\Profile;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;


class ProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'profiles';

    protected static ?string $title = "Profiles";
    protected static ?string $label = "profiles";

    public function form(Form $form): Form
    {
        return $form;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application.user.name'),
                Tables\Columns\TextColumn::make('application.display_name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn (Profile $record): string => ProfileResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
