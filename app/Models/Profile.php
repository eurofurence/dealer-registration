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

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public static function findByApplicationId(int|null $application_id): Profile|null
    {
        return self::where('application_id', $application_id)->first();
    }

    public static function getAllProfilesForExport()
    {
        return self::whereNotNull('offer_accepted_at')
            ->leftJoin('applications', 'applications.id', '=', 'profiles.application_id')
            ->leftJoin('users', 'users.id', '=', 'applications.user_id')
            ->select(
                'user_id',
                'name',
                'display_name',
                'table_number',
                'merchandise',
                'is_afterdark',
                'short_desc',
                'artist_desc',
                'art_desc',
                'profiles.website',
                'twitter',
                'telegram',
                'discord',
                'tweet',
                'art_preview_caption',
                'image_thumbnail',
                'image_art',
                'image_artist',
                'is_print',
                'is_artwork',
                'is_fursuit',
                'is_commissions',
                'is_misc',
                'attends_thu',
                'attends_fri',
                'attends_sat'
            )->get();
    }
}