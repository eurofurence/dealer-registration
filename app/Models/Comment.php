<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    const MAX_LENGTH = 4096;

    use HasFactory, HasUuids;
    public $timestamps = true;

    protected $primaryKey = 'uuid';

    protected $guarded = [];

    protected $casts = [
        'admin_only' => 'boolean',
        'text' => 'string',
    ];
    protected $attributes = [
        'admin_only' => false,
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getAllCommentsForExport()
    {
        $applications = self::leftJoin('applications', 'application_id', '=', 'applications.id')
            ->leftJoin('users as u1', 'applications.user_id', '=', 'u1.id')
            ->leftJoin('users as u2', 'comments.user_id', '=', 'u2.id')
            ->leftJoin('table_types', 'table_type_assigned', '=', 'table_types.id')
            ->select(
                'u1.name AS user',
                'applications.display_name AS display name',
                'applications.type AS type',
                'comments.text AS comment',
                'table_types.name AS assigned table',
                'applications.table_number AS table number',
                'u2.name AS author',
                'comments.admin_only AS admin only',
                'comments.created_at AS created at',
                'comments.updated_at AS updated at',
            )
            ->get();
        return json_decode(json_encode($applications), true);
    }
}
