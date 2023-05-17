<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\ProfileResource;
use App\Filament\Resources\UserResource;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\Profile;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;


class ProfileRelationManager extends RelationManager
{
    protected static string $relationship = 'profile';

    protected static ?string $title = "Profile";
    protected static ?string $label = "profile";
    protected static ?string $recordTitleAttribute = 'short_desc';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('short_desc')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('short_desc'),
                Tables\Columns\TextColumn::make('website'),
                Tables\Columns\TextColumn::make('attends_thu'),
                Tables\Columns\TextColumn::make('attends_fri'),
                Tables\Columns\TextColumn::make('attends_sat'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(Profile $record): string => ProfileResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([
            ]);
    }
}
