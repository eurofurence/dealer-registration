<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\UserResource;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class UserRelationManager extends RelationManager
{
    protected static string $relationship = 'user';

    protected static ?string $title = "User";
    protected static ?string $label = "user";
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('reg_id'),
                Tables\Columns\TextColumn::make('email'),

                Tables\Columns\TextColumn::make('packages booked')
                    ->badge()
                    ->getStateUsing(fn (?User $record): string => implode(array_replace(['none'], RegSysClientController::getPackages($record?->reg_id) ?? ['n/a']))),
                Tables\Columns\TextColumn::make('reg status')
                    ->badge()
                    ->getStateUsing(fn (?User $record): string => RegSysClientController::getSingleReg($record?->reg_id)['status'] ?? 'n/a')
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'primary',
                    })

            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('Show')
                    ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record])),

            ])
            ->bulkActions([]);
    }
}
