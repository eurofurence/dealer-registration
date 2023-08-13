<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\CommentResource;
use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class CommentRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';
    protected static ?string $title = "Comments";
    protected static ?string $label = "Comment";
    protected static ?string $recordTitleAttribute = 'text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('text')
                    ->required()
                    ->maxLength(4096),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author.name'),
                Tables\Columns\IconColumn::make('admin_only')
                    ->boolean(),
                Tables\Columns\TextColumn::make('text'),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('New comment')
                    ->url(fn () => CommentResource::getUrl('create'))
                    ->icon('heroicon-o-pencil'),
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(Comment $record): string => CommentResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([
            ]);
    }
}
