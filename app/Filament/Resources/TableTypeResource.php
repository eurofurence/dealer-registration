<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableTypeResource\Pages;
use App\Filament\Resources\TableTypeResource\RelationManagers;
use App\Models\TableType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TableTypeResource extends Resource
{
    protected static ?string $model = TableType::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('seats')
                    ->required(),
                Forms\Components\TextInput::make('package')
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->integer(true)->hint('in cents'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('seats')->sortable(),
                Tables\Columns\TextColumn::make('package')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->sortable(),
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
            'index' => Pages\ListTableTypes::route('/'),
            'create' => Pages\CreateTableType::route('/create'),
            'edit' => Pages\EditTableType::route('/{record}/edit'),
        ];
    }
}
