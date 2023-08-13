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
}
