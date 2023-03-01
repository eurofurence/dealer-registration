<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required(),
                Forms\Components\TextInput::make('table_type_requested')
                    ->required(),
                Forms\Components\TextInput::make('table_type_assigned'),
                Forms\Components\TextInput::make('parent'),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('display_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('website')
                    ->maxLength(255),
                Forms\Components\TextInput::make('table_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('invite_code_shares')
                    ->maxLength(255),
                Forms\Components\TextInput::make('invite_code_assistants')
                    ->maxLength(255),
                Forms\Components\Textarea::make('merchandise')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('wanted_neighbors')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('unwanted_neighbors')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('comment')
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_mature'),
                Forms\Components\Toggle::make('is_afterdark'),
                Forms\Components\Toggle::make('is_power'),
                Forms\Components\Toggle::make('is_wallseat'),
                Forms\Components\DateTimePicker::make('canceled_at'),
                Forms\Components\DateTimePicker::make('accepted_at'),
                Forms\Components\DateTimePicker::make('allocated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('table_type_requested'),
                Tables\Columns\TextColumn::make('table_type_assigned'),
                Tables\Columns\TextColumn::make('parent'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('display_name'),
                Tables\Columns\TextColumn::make('website'),
                Tables\Columns\TextColumn::make('table_number'),
                Tables\Columns\TextColumn::make('invite_code_shares'),
                Tables\Columns\TextColumn::make('invite_code_assistants'),
                Tables\Columns\TextColumn::make('merchandise'),
                Tables\Columns\TextColumn::make('wanted_neighbors'),
                Tables\Columns\TextColumn::make('unwanted_neighbors'),
                Tables\Columns\TextColumn::make('comment'),
                Tables\Columns\IconColumn::make('is_mature')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_afterdark')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_power')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_wallseat')
                    ->boolean(),
                Tables\Columns\TextColumn::make('canceled_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('accepted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('allocated_at')
                    ->dateTime(),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }    
}
