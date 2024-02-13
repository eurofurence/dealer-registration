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

        User::query()->whereNotNull('reg_id')->each(function (User $user) {
        });
    }

    public static function sync(array $userIds = null): bool
    {
        $registrations = RegSysClientController::getAllRegs();
        $userQuery = User::query();

        if ($userIds) {
            $userQuery->whereIn('id', $userIds);
        }

        return $userQuery->each(function (User $user) use ($registrations) {
            $regId = $user->reg_id;
            if ($registration = $registrations[$user->email] ?? null) {
                $regId = $registration['id'];
            } elseif (!empty($user->reg_id)) {
                $regId = null;
            }

            if ($regId != $user->reg_id) {
                if ($user->update(['reg_id' => $regId])) {
                    Log::info("Successfully updated registration id for user {$user->id} from '{$user->reg_id}' to '{$regId}'.");
                } else {
                    Log::warning("Failed to update registration id for user {$user->id} from '{$user->reg_id}' to '{$regId}'.");
                }
            }

            if ($regId !== null) {
                /**
                 * @var ?Application
                 */
                $application = $user->application()->first() ?? null;
                $applicationIsActive = $application?->isActive() ?? false;
                if (RegSysClientController::setAdditionalInfoDealerReg($regId, $applicationIsActive)) {
                    Log::info("Successfully " . ($applicationIsActive ? 'set' : 'cleared'). " dealer registration flag on registration system for user {$user->id} with registration ID {$regId}.");
                } else {
                    Log::warning("Failed to " . ($applicationIsActive ? 'set' : 'cleared'). " dealer registration flag on registration system for user {$user->id} with registration ID {$regId}.");
                }
            }
        });
    }
}
