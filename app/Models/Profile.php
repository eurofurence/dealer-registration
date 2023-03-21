<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

     protected $casts = [
      "is_print" => "boolean",
      "is_artwork" => "boolean",
      "is_fursuit" => "boolean",
      "is_commissions" => "boolean",
      "is_misc" => "boolean",
      "attends_thu" => "boolean",
      "attends_fri" => "boolean",
      "attends_sat" => "boolean",
    ];

    protected $attributes = [
        "attends_thu" => true,
        "attends_fri" => true,
        "attends_sat" => true,
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public static function findByApplicationId(int|null $application_id): Profile|null
    {
        return self::where('application_id', $application_id)->first();
    }
}
