<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Console\Helper\Table;

class Application extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = [
        "status"
    ];

    protected $with = [
        'requestedTable',
        'assignedTable',
    ];

    protected $casts = [
        "type" => ApplicationType::class,
        "is_power" => "boolean",
        "is_afterdark" => "boolean",
        "is_wallseat" => "boolean",
        "canceled_at" => "datetime",
        "waiting_at" => "datetime",
        "checked_in_at" => "datetime",
        "offer_sent_at" => "datetime",
        "offer_accepted_at" => "datetime",
        "is_notified" => "boolean",
    ];

    protected $attributes = [
        "table_type_requested" => 2
    ];

    protected static function boot()
    {
        parent::boot();
        // Automatically update application status from Open/Waiting to Table Offered on table type/number
        static::updating(function (Application $model) {
            if (
                // ignore if we have directly modified the application status in this request
                !$model->isDirty(['waiting_at', 'offer_sent_at', 'offer_accepted_at', 'checked_in_at', 'canceled_at'])
                && !$model->wasChanged(['waiting_at', 'offer_sent_at', 'offer_accepted_at', 'checked_in_at', 'canceled_at'])
                && (
                    // table number or type was changed
                    $model->isDirty('table_type_assigned') || $model->isDirty('table_number')
                    || $model->wasChanged('table_type_assigned') || $model->wasChanged('table_number')
                )
                // table number must not be empty
                && !empty($model->table_number)
                // table type must be assigned for dealer applications
                && ($model->type !== ApplicationType::Dealer || !empty($model->table_type_assigned))
                // status is applicable for automatic change (Open or Waiting)
                && ($model->status === ApplicationStatus::Open
                    || $model->status === ApplicationStatus::Waiting
                )
            ) {
                Log::info("Changing status of application {$model->id} from {$model->status->name} to TableOffered due to table type/name change.");
                $model->status = ApplicationStatus::TableOffered;
            }
        });

        // Update child applications to TableAccepted on parent accepting their offered table
        static::updated(function (Application $model) {
            if ($model->type === ApplicationType::Dealer && $model->wasChanged('offer_accepted_at') && !empty($model->offer_accepted_at)) {
                Log::info("Changing status of children of application {$model->id} to TableAccepted due to parent table having been accepted.");
                foreach ($model->children()->get() as $child) {
                    if ($child->status !== ApplicationStatus::Canceled) {
                        Log::info("Changing status of application {$child->id} to TableAccepted due to parent application {$model->id} having changed to this status.");
                        $child->status = ApplicationStatus::TableAccepted;
                    }
                }
            }
        });

        // Update table number of Assistants when their parent's table number is updated
        // DO NOT do this for Shares as it would automatically set them to TableOffered without review!
        static::updated(function (Application $model) {
            if ($model->type === ApplicationType::Dealer && $model->wasChanged('table_number') && !empty($model->table_number)) {
                Log::info("Changing table number of assistants of application {$model->id} to that of parent.");
                foreach ($model->children()->get() as $child) {
                    if ($child->status !== ApplicationStatus::Canceled && $child->type === ApplicationType::Assistant) {
                        Log::info("Changing table number of assistant application {$child->id} to {$model->table_number} due to parent application change.");
                        $child->table_number = $model->table_number;
                        $child->update();
                    }
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedTable()
    {
        return $this->belongsTo(TableType::class, 'table_type_requested');
    }

    public function assignedTable()
    {
        return $this->belongsTo(TableType::class, 'table_type_assigned');
    }

    public function parent()
    {
        return $this->belongsTo(__CLASS__, 'parent');
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent');
    }

    public function getStatus()
    {
        if (!is_null($this->canceled_at)) {
            return ApplicationStatus::Canceled;
        }
        if (!is_null($this->checked_in_at)) {
            return ApplicationStatus::CheckedIn;
        }
        if (!is_null($this->waiting_at)) {
            return ApplicationStatus::Waiting;
        }
        if (!is_null($this->offer_accepted_at)) {
            return ApplicationStatus::TableAccepted;
        }
        if (!is_null($this->offer_sent_at)) {
            return ApplicationStatus::TableOffered;
        }
        return ApplicationStatus::Open;
    }

    public function isActive()
    {
        return $this->getStatus() !== ApplicationStatus::Canceled;
    }

    public function cancel()
    {
        $this->canceled_at = now();
        $this->save();
    }

    public function getActiveShares(): int
    {
        return $this->children()->whereNull('canceled_at')->where('type', ApplicationType::Share)->count();
    }

    public function getActiveAssistants(): int
    {
        return $this->children()->whereNull('canceled_at')->where('type', ApplicationType::Assistant)->count();
    }

    public function getAvailableShares(): int
    {
        if (is_null($this->requestedTable)) {
            return 0;
        }
        $countedAssistants = $this->getActiveShares() < $this->requestedTable->seats - 1 ? $this->getActiveAssistants() : max($this->getActiveAssistants() - 1, 0);
        return !is_null($this->requestedTable) ? max($this->requestedTable->seats - 1 - $countedAssistants, 0) : 0;
    }

    public function getAvailableAssistants(): int
    {
        return !is_null($this->requestedTable) ? max(1, $this->requestedTable->seats - 1 - $this->getActiveShares()) : 0;
    }

    public function getFreeShares(): int
    {
        return !is_null($this->requestedTable) ?  $this->getAvailableShares() - $this->getActiveShares() : 0;
    }

    public function getFreeAssistants(): int
    {
        return $this->getAvailableAssistants() - $this->children()->whereNull('canceled_at')->where('type', ApplicationType::Assistant)->count();
    }

    public static function determineApplicationTypeByCode(string|null $code): ApplicationType
    {
        $applicationType = ApplicationType::Dealer;
        if (!is_null($code)) {
            $application = self::findByCode($code);
            if ($application === null) {
                return $applicationType;
            }

            if ($application->invite_code_shares === $code) {
                $applicationType = ApplicationType::Share;
            }
            if ($application->invite_code_assistants === $code) {
                $applicationType = ApplicationType::Assistant;
            }
        }
        return $applicationType;
    }

    public function getStatusAttribute()
    {
        return $this->getStatus();
    }

    public function setStatusAttribute(ApplicationStatus|string $status)
    {
        if (is_string($status)) {
            $status = ApplicationStatus::tryFrom($status);
        }

        // Don't reset timestamps when application is saved without status change!
        if ($this->getStatus() === $status) {
            return;
        }

        if ($status === ApplicationStatus::Canceled) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'parent' => null,
                'waiting_at' => null,
                'type' => ApplicationType::Dealer,
                'canceled_at' => now(),
            ]);
        }

        if ($status === ApplicationStatus::Open) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::Waiting) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'waiting_at' => now(),
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::TableOffered) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::TableAccepted) {
            $this->update([
                'offer_accepted_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if ($status === ApplicationStatus::CheckedIn) {
            $this->update([
                'checked_in_at' => now(),
                'canceled_at' => null,
            ]);
        }
    }

    public static function findByCode(string|null $code): Application|null
    {
        return self::where('type', ApplicationType::Dealer)
            ->where(function ($q) use ($code) {
                return $q->where('invite_code_assistants', $code)
                    ->orWhere('invite_code_shares', $code);
            })->first();
    }

    public static function findByUserId(int|null $user_id): Application|null
    {
        return self::where('user_id', $user_id)->first();
    }

    public static function getEligibleParents()
    {
        return self::leftJoin('users', 'user_id', '=', 'users.id')
            ->where('type', '=', 'dealer')
            ->whereNull('canceled_at')
            ->select(
                'applications.id AS id',
                'users.name AS name'
            )->get();
    }

    public static function getAllApplicationsForExport()
    {
        $applications = self::leftJoin('profiles', 'applications.id', '=', 'profiles.application_id')
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->leftJoin('table_types AS t1', 'table_type_requested', '=', 't1.id')
            ->leftJoin('table_types AS t2', 'table_type_assigned', '=', 't2.id')
            ->select(
                'applications.id AS app_id',
                'users.name AS user_name',
                'type AS app_type',
                'parent',
                'display_name',
                'applications.website AS app_website',
                'table_number',
                'merchandise',
                'invite_code_shares',
                'invite_code_assistants',
                'wanted_neighbors',
                'comment',
                'is_afterdark',
                'is_power',
                'is_wallseat',
                't1.name AS table_type_requested',
                't2.name AS table_type_assigned',
                'is_notified',
                'applications.created_at AS app_created_at',
                'applications.updated_at AS app_updated_at',
                'waiting_at',
                'offer_sent_at',
                'offer_accepted_at',
                'checked_in_at',
                'canceled_at',
                'profiles.*',
            )
            ->get();

        return json_decode(json_encode($applications), true);
    }

    public function setIsNotified($isNotified)
    {
        $this->is_notified = $isNotified;
        $this->save();
    }
}
