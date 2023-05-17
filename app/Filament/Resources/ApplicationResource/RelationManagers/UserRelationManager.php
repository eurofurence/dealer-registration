<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'user';

    protected static ?string $title = "User";
    protected static ?string $label = "user";
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('reg_id'),
                Tables\Columns\TextColumn::make('email'),

                Tables\Columns\BadgeColumn::make('packages booked')
                    ->formatStateUsing(fn(?User $record): string => implode(RegSysClientController::getPackages($record->reg_id)) ?? ''),
                Tables\Columns\BadgeColumn::make('reg status')
                    ->formatStateUsing(fn(?User $record): string => RegSysClientController::getSingleReg($record->reg_id)['status'] ?? '')
                    ->colors([
                        'secondary',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ])

            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn(User $record): string => UserResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([
            ]);
    }
}
