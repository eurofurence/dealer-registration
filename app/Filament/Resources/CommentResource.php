<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Group::make()->columnSpan(2)->columns()->schema([
                    Forms\Components\Group::make()->columns(1)->schema([
                        Forms\Components\Select::make('user_id')->searchable()->relationship('author', 'name')
                            ->required(),
                        Forms\Components\Select::make('application_id')->searchable()->relationship('application', 'id')
                            ->required(),
                        Forms\Components\Toggle::make('admin_only'),
                    ]),
                    Forms\Components\Textarea::make('text')
                            ->required()
                            ->maxLength(2048),
                ]),
                Forms\Components\Fieldset::make('Dates')->columnSpan(1)->columns()->schema([
                    Forms\Components\Placeholder::make('created_at')->content(fn (?Comment $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                    Forms\Components\Placeholder::make('updated_at')->content(fn (?Comment $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')->hidden()->url(fn($record) => CommentResource::getUrl('edit', ['record' => $record->uuid])),
                Tables\Columns\TextColumn::make('application_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('admin_only')
                    ->boolean(),
                Tables\Columns\TextColumn::make('text'),
            ])
            ->filters([
                Tables\Filters\Filter::make('admin_only')
                    ->query(fn(Builder $query): Builder => $query->where('admin_only', '=', '1'))
                    ->label('Is Admin only'),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
