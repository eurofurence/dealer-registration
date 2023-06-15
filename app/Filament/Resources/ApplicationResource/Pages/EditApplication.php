<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Enums\StatusNotificationResult;
use App\Filament\Resources\ApplicationResource;
use App\Http\Controllers\Applications\ApplicationController;
use App\Models\Application;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('Send status notification')
                ->action('sendStatusNotification')
                ->requiresConfirmation()
                ->icon('heroicon-o-mail'),
            Actions\Action::make('Send reminder')
                ->action('sendReminderNotification')
                ->requiresConfirmation()
                ->icon('heroicon-o-mail'),
        ];
    }

    public function getRecord(): Application
    {
        return parent::getRecord();
    }

    public function sendStatusNotification()
    {
        $application = $this->getRecord();
        $user = $application->user()->first();
        $result = ApplicationController::sendStatusNotification($application);
        $frontendNotification = Notification::make();

        switch ($result) {
            case StatusNotificationResult::Accepted:
                $frontendNotification->title('Notification sent')
                    ->body("Notified application {$application->id} of user {$user->name} about being accepted with their requested table type.")
                    ->success();
                break;
            case StatusNotificationResult::OnHold:
                $frontendNotification->title('Notification sent')
                    ->body("Notified application {$application->id} of user {$user->name} about being offered a different table type than they requested (on-hold).")
                    ->success();
                break;
            case StatusNotificationResult::WaitingList:
                $frontendNotification->title('Notification sent')
                    ->body("Notified application {$application->id} of user {$user->name} about being put on the waiting list.")
                    ->success();
                break;
            case StatusNotificationResult::SharesInvalid:
                $frontendNotification->title('Notification not sent')
                    ->body("Application not notified because some uncanceled shares have not been assigned to the same table number!")
                    ->danger();
                break;
            case StatusNotificationResult::StatusNotApplicable:
                $frontendNotification->title('Notification not sent')
                    ->body("No applicable notification for current status '{$application->status->value}' or type '{$application->type->value}' of application {$application->id} of user {$user->name}.")
                    ->warning();
                break;
            case StatusNotificationResult::NotDealer:
                $frontendNotification->title('Notification not sent')
                    ->body("Did not notify application {$application->id} of user {$user->name} because they are of type {$application->type->value}.")
                    ->danger();
                break;
            default:
                $frontendNotification->title('Error')
                    ->body("Unexpected return value from ApplicationController::sendStatusNotification! Please inform the developers: [application={$application->id},result={$result->name}]")
                    ->danger();
                break;
        }

        $frontendNotification->persistent()->send();
        $this->refreshFormData(['status']);
    }


    public function sendReminderNotification()
    {
        $application = $this->getRecord();
        $user = $application->user()->first();
        $result = ApplicationController::sendReminderNotification($application);
        $frontendNotification = Notification::make();

        switch ($result) {
            case StatusNotificationResult::Accepted:
                $frontendNotification->title('Notification sent')
                    ->body("Sent reminder for application {$application->id} of user {$user->name} to confirm their assigned table.")
                    ->success();
                break;
            case StatusNotificationResult::NotDealer:
                $frontendNotification->title('Notification not sent')
                    ->body("Did not send reminder {$application->id} of user {$user->name} because they are of type {$application->type->value}.")
                    ->danger();
                break;
            case StatusNotificationResult::StatusNotApplicable:
                $frontendNotification->title('Notification not sent')
                    ->body("Reminder not applicable for current status '{$application->status->value}' or type '{$application->type->value}' of application {$application->id} of user {$user->name}.")
                    ->warning();
                break;
            default:
                $frontendNotification->title('Error')
                    ->body("Unexpected return value from ApplicationController::sendStatusNotification! Please inform the developers: [application={$application->id},result={$result->name}]")
                    ->danger();
                break;
        }

        $frontendNotification->persistent()->send();
    }
}
