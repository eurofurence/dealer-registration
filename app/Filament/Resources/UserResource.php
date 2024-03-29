<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Http\Controllers\Client\RegSysClientController;
use App\Jobs\SynchronizeRegsys;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reg_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('identity_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Fieldset::make('Reg status')->inlineLabel()->columns(1)->schema([
                    Forms\Components\Placeholder::make('Packages booked')
                        ->content(fn (?User $record): string => implode(array_replace(['none'], RegSysClientController::getPackages($record?->reg_id) ?? ['n/a']))),
                    Forms\Components\Placeholder::make('Registration status')
                        ->content(fn (?User $record): string => RegSysClientController::getSingleReg($record?->reg_id)['status'] ?? 'n/a'),
                    Forms\Components\Placeholder::make('Flagged as active application?')
                        ->content(fn (?User $record): string => (($hasFlag = RegSysClientController::getAdditionalInfoDealerReg($record?->reg_id)) === null ? 'n/a' : ($hasFlag ? 'yes' : 'no'))),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reg_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('identity_id'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->url(fn (?User $record) => "mailto:{$record->email}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('reg_id')->query(fn (Builder $query): Builder => $query->whereNull('reg_id'))->label('Missing Reg ID'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('Sync with Regsys')
                    ->tooltip('Retrieve registration IDs from Regsys and publish to Regsys if user has an active application')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        SynchronizeRegsys::sync($records->pluck('id')->all());
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
