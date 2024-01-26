<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileResource\Pages;
use App\Filament\Resources\ProfileResource\RelationManagers;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ProfileResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()->columnSpan(2)->columns()->schema([
                    Forms\Components\TextInput::make('application_id'),

                    Forms\Components\TextInput::make('website')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('twitter')
                        ->maxLength(1024),
                    Forms\Components\TextInput::make('telegram')
                        ->maxLength(1024),
                    Forms\Components\TextInput::make('discord')
                        ->maxLength(1024),
                    Forms\Components\TextInput::make('tweet')
                        ->maxLength(280),
                    Forms\Components\TextInput::make('art_preview_caption')
                        ->maxLength(255),

                    Forms\Components\Fieldset::make('Images')->inlineLabel()->columns(2)->schema([
                        Forms\Components\FileUpload::make('image_thumbnail')
                            ->image()
                            ->imageResizeMode('force')
                            ->imageResizeTargetWidth('60')
                            ->imageResizeTargetHeight('60'),
                        Forms\Components\Textarea::make('short_desc')
                            ->maxLength(1024),
                        Forms\Components\FileUpload::make('image_artist')
                            ->image()
                            ->imageResizeMode('force')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400'),
                        Forms\Components\Textarea::make('artist_desc')
                            ->maxLength(2048),
                        Forms\Components\FileUpload::make('image_art')
                            ->image()
                            ->imageResizeMode('force')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('450'),
                        Forms\Components\Textarea::make('art_desc')
                            ->maxLength(2048),
                    ]),
                ]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Fieldset::make('Attendance')->inlineLabel()->columns(1)->schema([
                        Forms\Components\Toggle::make('attends_thu'),
                        Forms\Components\Toggle::make('attends_fri'),
                        Forms\Components\Toggle::make('attends_sat'),
                    ]),
                ]),
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
                Tables\Columns\TextColumn::make('image_thumbnail'),
                Tables\Columns\TextColumn::make('image_artist'),
                Tables\Columns\TextColumn::make('image_art'),
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
            RelationManagers\ApplicationRelationManager::class,
            RelationManagers\KeywordRelationManager::class
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
