<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\BadgeResult;
use App\Filament\Resources\BadgeResource\Pages;
use App\Models\Application;
use App\Services\BadgeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BadgeResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $label = 'Badge';
    protected static ?string $navigationLabel = 'Badges';
    public static ?string $slug = 'badges';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Group::make()->columns(2)->schema([
                    Forms\Components\Select::make('badge_type')
                        ->required()
                        ->live()
                        ->label(__("Badge Type"))
                        ->helperText(
                            str('Select the type to display on the badge, use custom for free text input.')->inlineMarkdown()->toHtmlString()
                        )
                        ->options([
                            ApplicationType::Assistant->value => 'Assistant',
                            ApplicationType::Dealer->value => 'Dealer',
                            // ApplicationType::Share->value => 'Share',
                            'goh' => 'Guest of Honor',
                            'staff' => 'Staff',
                            'custom' => 'Custom'
                        ])
                        ->dehydrated(fn($state) => $state !== 'custom')
                        ->native(false),
                    Forms\Components\TextInput::make('badge_type_custom')
                        ->label(__("Custom Badge Type"))
                        ->helperText(
                            str('Custom type to display on the badge, be mindful of the width in the template!')->inlineMarkdown()->toHtmlString()
                        )
                        ->visible(fn(Forms\Get $get) => $get('badge_type') === 'custom')
                        ->requiredIf('badge_type', 'custom'),
                ]),
                Forms\Components\Group::make()->columns(2)->schema([
                    Forms\Components\Toggle::make('has_table')
                        ->live()
                        ->label(__("Show table number"))
                        ->helperText(
                            str('Should the badge have a table number visible?')->inlineMarkdown()->toHtmlString()
                        ),
                    Forms\Components\TextInput::make('table_number')
                        ->visible(fn(Forms\Get $get) => $get('has_table') === true)
                        ->requiredIf('has_table', true),
                ]),
                Forms\Components\Group::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('reg_id')
                        ->required()
                        ->label(__("Reg ID"))
                        ->numeric()
                        ->helperText(
                            str('Use only numbers.')->inlineMarkdown()->toHtmlString()
                        ),
                    Forms\Components\TextInput::make('display_name')
                        ->required()
                        ->label(__("Display Name"))
                        ->helperText(
                            str('Limit to 40 characters or less.')->inlineMarkdown()->toHtmlString()
                        ),
                ]),
                Forms\Components\Toggle::make('has_share')
                    ->label(__("Show share indicator"))
                    ->helperText(
                        str('Adds an `S` next to the Reg ID.')->inlineMarkdown()->toHtmlString()
                    ),
                Forms\Components\Toggle::make('double_sided')
                    ->label(__("Double sided badges"))
                    ->helperText(str('Should the print go on both sides of the card?')->inlineMarkdown()->toHtmlString()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_ready')
                    ->label(__('Ready'))
                    ->getStateUsing(function (Application $record) {
                        return $record->isReady();
                    })
                    ->boolean(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function (ApplicationType $state) {
                        return ucfirst($state->value);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('table_number')
                    ->sortable()
                    ->searchable()
                    ->disabled(fn($record) => $record->type === ApplicationType::Assistant),
                Tables\Columns\TextColumn::make('display_name')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('dealers')
                    ->query(fn(Builder $query): Builder => $query->where('type', 'dealer'))
                    ->label(__('Only Dealerships')),
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(array_column(ApplicationStatus::cases(), 'value'), array_column(ApplicationStatus::cases(), 'name')))
                    ->query(function (Builder $query, array $data) {
                        if (!key_exists('values', $data)) {
                            return;
                        }
                        $query->where(function (Builder $query) use ($data) {
                            foreach ($data['values'] as $value) {
                                ApplicationStatus::tryFrom($value)?->orWhere($query);
                            }
                        });
                    })
                    ->multiple(),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkAction::make('print')
                    ->form([
                        Forms\Components\Toggle::make('double_sided')
                            ->label(__("Double sided badges"))
                            ->helperText(str('Should the print go on both sides of the card?')->inlineMarkdown()->toHtmlString())

                    ])
                    ->action(function (Collection $records, array $data, Tables\Actions\BulkAction $action) {
                        $badgeService = new BadgeService();
                        $doubleSided = $data['double_sided'];
                        foreach ($records as $record) {
                            $badgeService->generateBadge(
                                $record,
                                $doubleSided
                            );
                        }

                        $badgeFile = tmpfile();
                        $badgeFileUri = stream_get_meta_data($badgeFile)['uri'];
                        $badgeService->save($badgeFile);
                        fflush($badgeFile);

                        $action->success();

                        // Note, we need to pass the $badgeFile to the closure so PHP does not remove it prematurely
                        return response()->streamDownload(function () use ($badgeFile, $badgeFileUri) {
                            echo file_get_contents($badgeFileUri);
                        }, "DD Badges - " . date("Y-m-d\TH-i-sO") . ".pdf");
                    })
                    ->successNotificationTitle("Bogos binted")
                    ->icon('heroicon-o-printer'),
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
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\PrintBadge::route('/create'),
        ];
    }
}
