<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\User;
use Filament\Forms;

use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
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
                    Forms\Components\Placeholder::make('packages booked')
                        ->content(fn(?User $record): string => implode(RegSysClientController::getPackages($record->reg_id)) ?? '-'),
                    Forms\Components\Placeholder::make('reg status')
                        ->content(fn(?User $record): string => RegSysClientController::getSingleReg($record->reg_id)['status'] ?? '-'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reg_id'),
                Tables\Columns\TextColumn::make('identity_id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email')
                    ->url(fn(?User $record) => "mailto:{$record->email}"),
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
                Tables\Actions\BulkAction::make('Update reg ids')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $regs = RegSysClientController::getAllRegs();
                        foreach ($records as $record) {
                            $found = false;
                            foreach ($regs as $reg) {
                                if ($record->email == $reg['email']) {
                                    $record->reg_id = $reg['id'];
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $record->reg_id = null;
                            }
                            $record->save();

                        }
                    }),
            ])
        ;
    }

    public static function getRelations(): array
    {
        return [
            // new ChildrenRelationManager()
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
