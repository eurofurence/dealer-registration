<?php

namespace App\Jobs;

use App\Http\Controllers\Client\RegSysClientController;
use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SynchronizeRegsys implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        self::sync();
    }

    public static function sync(array $userIds = null): bool
    {
        return self::fetchMissingRegistrationIds($userIds)
            && self::updateAdditionalInfoDealerReg($userIds);
    }

    private static function fetchMissingRegistrationIds(array $userIds = null): bool
    {
        $registrations = RegSysClientController::getAllRegs();
        $userQuery = User::query()->whereNull('reg_id');

        if ($userIds) {
            $userQuery->whereIn('id', $userIds);
        }

        return $userQuery->each(function (User $user) use ($registrations) {
            $registration = $registrations[$user->email] ?? null;

            if ($registration === null) {
                return;
            }

            $registrationId = $registration['id'] ?? null;

            if ($registrationId === null) {
                return;
            }

            if ($user->update(['reg_id' => $registrationId])) {
                Log::info("Successfully added registration id '{$registrationId}' to user {$user->id}.");
            } else {
                Log::warning("Failed to add registration id '{$registrationId}' to user {$user->id}.");
            }
        });
    }

    private static function updateAdditionalInfoDealerReg(array $userIds = null)
    {
        $userQuery = User::query()->whereNotNull('reg_id');

        if ($userIds) {
            $userQuery->whereIn('id', $userIds);
        }

        $userQuery->each(function (User $user) {
            /**
             * @var ?Application
             */
            $application = $user->application()->first() ?? null;
            $applicationIsActive = $application?->isActive() ?? false;
            $registrationId = $user->reg_id;

            if (RegSysClientController::getAdditionalInfoDealerReg($registrationId) === $applicationIsActive) {
                // No need to update flag without change
                return;
            }

            if (RegSysClientController::setAdditionalInfoDealerReg($registrationId, $applicationIsActive)) {
                Log::info("Successfully " . ($applicationIsActive ? 'set' : 'cleared') . " dealer registration flag on registration system for user {$user->id} with registration ID {$registrationId}.");
            } else {
                Log::warning("Failed to " . ($applicationIsActive ? 'set' : 'clear') . " dealer registration flag on registration system for user {$user->id} with registration ID {$registrationId}.");
            }
        });
    }
}
