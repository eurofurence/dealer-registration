<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\CommentResource;
use App\Filament\Resources\CommentResource\Pages\CreateComment;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\Select;
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
                //
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
                    ->form([
                        Forms\Components\TextInput::make('application_id')
                            ->default(function (RelationManager $livewire) {
                                return $livewire->ownerRecord->id;
                            })
                            ->required(),
                        Forms\Components\TextInput::make('user_id')
                            ->label("User ID (Author)")
                            ->default(\Auth::user()->id)
                            ->required(),
                        Forms\Components\Toggle::make('admin_only'),
                        Forms\Components\Textarea::make('text')
                            ->required()
                            ->maxLength(4096),

                    ])
                    ->action(function (array $data): void {
                        Comment::create([
                            'text' => $data['text'],
                            'admin_only' => $data['admin_only'],
                            'application_id' => $data['application_id'],
                            'user_id' => $data['user_id'],
                        ]);
                    })
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
