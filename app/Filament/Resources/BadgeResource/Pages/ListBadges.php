<?php

namespace App\Filament\Resources\BadgeResource\Pages;

use App\Filament\Resources\BadgeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListBadges extends ListRecords
{
    protected static string $resource = BadgeResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        $settingsAction = Actions\Action::make("settings")
                    ->label(__("Settings"))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn () => BadgeResource::getUrl('settings'));

        
        // Check if everything looks good
        $badge_font = Storage::disk('local')->exists('badges/badge-font');
        $badge_background = Storage::disk('local')->exists('badges/badge-background'); 
        if (!$badge_font || !$badge_background) {
            $settingsAction = $settingsAction
                                ->color("danger")
                                ->icon('heroicon-o-exclamation-triangle');
        }

        // Yeet
        $actions[] = $settingsAction;

        $actions[] = Actions\CreateAction::make()
                    ->icon('heroicon-o-paint-brush')
                    ->label(__("Custom Badge"));
        return $actions;
    }
}
