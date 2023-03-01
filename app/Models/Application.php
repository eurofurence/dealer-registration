<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Helper\Table;

class Application extends Model
{
    protected $guarded = [];
    protected $casts = [
      "type" => ApplicationType::class,
      "is_power" => "boolean",
      "is_afterdark" => "boolean",
      "is_wallseat" => "boolean",
      "is_mature" => "boolean",
      "canceled_at" => "datetime",
      "allocated_at" => "datetime",
      "accepted_at" => "datetime",

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
        if(!is_null($this->allocated_at) && !is_null($this->accepted_at)) {
            return ApplicationStatus::Allocated;
        }
        if(!is_null($this->accepted_at)) {
            return ApplicationStatus::Accepted;
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
}
