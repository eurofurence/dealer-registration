<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $attributes = [
        "table_type_requested" => 2
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestedTable()
    {
        return $this->belongsTo(TableType::class,'table_type_requested');
    }

    public function assignedTable()
    {
        return $this->belongsTo(TableType::class,'table_type_assigned');
    }

    public function parent()
    {
        return $this->belongsTo(__CLASS__,'parent');
    }

    public function children()
    {
        return $this->hasMany(__CLASS__,'parent');
    }

    public function getStatus()
    {
        if(!is_null($this->canceled_at)) {
            return ApplicationStatus::Canceled;
        }
        if(!is_null($this->checked_in_at)) {
            return ApplicationStatus::CheckedIn;
        }
        if(!is_null($this->waiting_at)) {
            return ApplicationStatus::Waiting;
        }
        if(!is_null($this->offer_accepted_at)) {
            return ApplicationStatus::TableAccepted;
        }
        if(!is_null($this->offer_sent_at)) {
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
        return $this->children()->whereNull('canceled_at')->where('type',ApplicationType::Share)->count();
    }

    public function getActiveAssistants(): int
    {
        return $this->children()->whereNull('canceled_at')->where('type',ApplicationType::Assistant)->count();
    }

    public function getAvailableShares(): int
    {
        return !is_null($this->requestedTable) ? max($this->requestedTable->seats - 1 - $this->getActiveAssistants(), 0) : 0;        
    }

    public function getAvailableAssistants(): int
    {
        return !is_null($this->requestedTable) ? max(1, $this->requestedTable->seats - 1 - $this->getActiveShares()) : 0;
    }

    public function getFreeShares(): int
    {
        return !is_null($this->requestedTable) ?  $this->requestedTable->seats - 1 - $this->getActiveShares() - $this->getActiveAssistants() : 0;
    }

    public function getFreeAssistants(): int
    {
        return $this->getAvailableAssistants() - $this->children()->whereNotNull('canceled_at')->where('type',ApplicationType::Assistant)->count();
    }

    public static function determineApplicationTypeByCode(string|null $code): ApplicationType
    {
        $applicationType = ApplicationType::Dealer;
        if (!is_null($code)) {
            $application = self::findByCode($code);
            if($application === null) {
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
        if(is_string($status)) {
            $status = ApplicationStatus::tryFrom($status);
        }
        if($status === ApplicationStatus::Canceled) {
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

        if($status === ApplicationStatus::Open) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'table_number' => null,
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if($status === ApplicationStatus::Waiting) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => null,
                'waiting_at' => now(),
                'canceled_at' => null,
            ]);
        }

        if($status === ApplicationStatus::TableOffered) {
            $this->update([
                'offer_accepted_at' => null,
                'offer_sent_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if($status === ApplicationStatus::TableAccepted) {
            $this->update([
                'offer_accepted_at' => now(),
                'waiting_at' => null,
                'canceled_at' => null,
            ]);
        }

        if($status === ApplicationStatus::CheckedIn) {
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
}
