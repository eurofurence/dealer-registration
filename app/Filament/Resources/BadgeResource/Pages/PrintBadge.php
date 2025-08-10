<?php

namespace App\Filament\Resources\BadgeResource\Pages;

use App\Filament\Resources\BadgeResource;
use App\Services\BadgeService;
use App\Http\Controllers\BadgeController;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Component;

class PrintBadge extends CreateRecord
{
    protected static string $resource = BadgeResource::class;
    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')->action(function (Component $livewire) {
                $data = $livewire->data;
                $type = 'Custom';
                if (array_key_exists('badge_type', $data) && !is_null($data['badge_type'])) {
                    $type = $data['badge_type'];
                }
                if (array_key_exists('badge_type_custom', $data) && !is_null($data['badge_type_custom'])) {
                    $type = $data['badge_type_custom'];
                }
                $regId = $data['reg_id'];
                $displayName = $data['display_name'];
                $tableNumber = null;
                if ($data['has_table']) {
                    $tableNumber = $data['table_number'];
                }
                $shareIndicator = $data['has_share'];
                $doubleSided = $data['double_sided'];

                $badgeService = new BadgeService();
                $badgeService->generateCustomBadge(
                    $type,
                    $regId,
                    $displayName,
                    $tableNumber,
                    $shareIndicator,
                    $doubleSided
                );

                $badgeFile = tmpfile();
                $badgeFileUri = stream_get_meta_data($badgeFile)['uri'];
                $badgeService->save($badgeFile);
                fflush($badgeFile);

                // Note, we need to pass the $badgeFile to the closure so PHP does not remove it prematurely
                return response()->streamDownload(function () use ($badgeFile, $badgeFileUri) {
                    echo file_get_contents($badgeFileUri);
                }, "DD Badge - " . date("Y-m-d\TH-i-sO") . " - $regId $displayName.pdf");
            }),
        ];
    }

    public function create(bool $another = false): void
    {
        // KLUDGE: A dummy since we use the form action
        $this->getCreatedNotification()?->send();
    }
}
