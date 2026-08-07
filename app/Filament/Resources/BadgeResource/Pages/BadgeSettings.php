<?php

namespace App\Filament\Resources\BadgeResource\Pages;

use App\Filament\Resources\BadgeResource;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BadgeSettings extends Page implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithFormActions, HasUnsavedDataChangesAlert;

    protected static string $resource = BadgeResource::class;

    protected static string $view = 'filament.resources.badge-resource.pages.badge-settings';

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(static::getResource()::canCreate(), 403);

        $this->form->fill();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save'))
                ->submit('save')
                ->keyBindings(['mod+s'])
        ];
    }

    public function form(Form $form): Form
    {
        $badge_font = Storage::disk('local')->exists('badges/badge-font');
        $badge_background = Storage::disk('local')->exists('badges/badge-background'); 
        return $form
            ->columns(1)
            ->schema(
                [
                    FileUpload::make('badge_font')
                        ->label(__("Badge Font"))
                        ->helperText("Present on disk: " . ($badge_font ? 'Yes' : 'No'))
                        ->required()
                        ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => "badge-font")
                        ->disk("local")
                        ->directory("badges")
                        ->default('badge-font'),
                    FileUpload::make('badge_background')
                        ->label(__("Badge Background"))
                        ->helperText("Present on disk: " . ($badge_background ? 'Yes' : 'No'))
                        ->required()
                        ->getUploadedFileNameForStorageUsing(fn(TemporaryUploadedFile $file): string => "badge-background")
                        ->disk("local")
                        ->directory("badges")
                        ->default('badge-background')
                        ->image(),
                ]
            )
            ->statePath('data');
    }

    public function create(bool $another = false): void
    {
        // Force processing of the uploaded files
        $this->form->getState();


        if (Storage::disk('local')->exists('badges/badgefont.json')) {
            // Delete the old processed font
            Storage::disk('local')->delete('badges/badgefont.json');
            Storage::disk('local')->delete('badges/badgefont.ctg.z');
            Storage::disk('local')->delete('badges/badgefont.z');
        }

        // Try to import the font immediately, this makes font errors more apparent
        try {
            new \Com\Tecnick\Pdf\Font\Import(Storage::disk('local')->path('badges/badge-font'));
        }
        catch (\Com\Tecnick\File\Exception $e) {
            Notification::make()
                ->danger()
                ->title("Font file not found, server side error? ".$e->getMessage())
                ->send();
                return;
        }
        catch (\Com\Tecnick\Pdf\Font\Exception $e) {
            Notification::make()
                ->danger()
                ->title("Failed to import font, parser reports: ".$e->getMessage())
                ->send();
                return;
        }

        Notification::make()
            ->success()
            ->title("Bogos Binted")
            ->send();
    }
}
