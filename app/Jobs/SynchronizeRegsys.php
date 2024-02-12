<?php

namespace App\Jobs;

use App\Http\Controllers\Client\RegSysClientController;
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
        self::syncRegistrationIds();
    }

    public static function syncRegistrationIds(array $userIds = null): void
    {
        $registrations = RegSysClientController::getAllRegs();
        $userQuery = User::query();

        if ($userIds) {
            $userQuery->whereIn('id', $userIds);
        }

        $userQuery->each(function (User $user) use ($registrations) {
            if (
                ($registration = $registrations[$user->email] ?? null)
                && $user->reg_id !== $registration['id']
            ) {
                $user->update([
                    'reg_id' => $registration['id'],
                ]);
            } elseif (!empty($user->reg_id)) {
                $user->update([
                    'reg_id' => null,
                ]);
            }
        });
    }
}
