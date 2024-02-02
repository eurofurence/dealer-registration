<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "attends_thu" => "boolean",
        "attends_fri" => "boolean",
        "attends_sat" => "boolean",
        "is_hidden" => "boolean",
    ];

    protected $attributes = [
        "attends_thu" => true,
        "attends_fri" => true,
        "attends_sat" => true,
        "is_hidden" => false,
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function categories()
    {
        return Category::whereIn('id', $this->keywords()->pluck('category_id')->toArray())->orderBy('name', 'asc');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_profile')->withTimestamps()->orderBy('name', 'asc');
    }

    public function keywordIds()
    {
        return $this->keywords()->pluck('id')->toArray();
    }

    public static function findByApplicationId(int|null $application_id): Profile|null
    {
        return self::where('application_id', $application_id)->first();
    }
}
