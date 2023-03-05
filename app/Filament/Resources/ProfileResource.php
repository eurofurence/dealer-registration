<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-annotation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('application_id'),
                Forms\Components\Textarea::make('short_desc')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('artist_desc')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('art_desc')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('website')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('twitter')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('telegram')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('discord')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('tweet')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('art_preview_caption')
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_print'),
                Forms\Components\Toggle::make('is_artwork'),
                Forms\Components\Toggle::make('is_fursuit'),
                Forms\Components\Toggle::make('is_commissions'),
                Forms\Components\Toggle::make('is_misc'),
                Forms\Components\Toggle::make('attends_thu'),
                Forms\Components\Toggle::make('attends_fri'),
                Forms\Components\Toggle::make('attends_sat'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_id'),
                Tables\Columns\TextColumn::make('short_desc'),
                Tables\Columns\TextColumn::make('artist_desc'),
                Tables\Columns\TextColumn::make('art_desc'),
                Tables\Columns\TextColumn::make('website'),
                Tables\Columns\TextColumn::make('twitter'),
                Tables\Columns\TextColumn::make('telegram'),
                Tables\Columns\TextColumn::make('discord'),
                Tables\Columns\TextColumn::make('tweet'),
                Tables\Columns\TextColumn::make('art_preview_caption'),
                Tables\Columns\IconColumn::make('is_print')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_artwork')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_fursuit')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_commissions')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_misc')
                    ->boolean(),
                Tables\Columns\IconColumn::make('attends_thu')
                    ->boolean(),
                Tables\Columns\IconColumn::make('attends_fri')
                    ->boolean(),
                Tables\Columns\IconColumn::make('attends_sat')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfiles::route('/'),
            'create' => Pages\CreateProfile::route('/create'),
            'edit' => Pages\EditProfile::route('/{record}/edit'),
        ];
    }
}
