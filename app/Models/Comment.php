<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, HasUuids;
    public $timestamps = true;
    protected $casts = [
        'adminOnly' => 'boolean',
        'text' => 'string',
    ];
    protected $attributes = [
        'adminOnly' => false,
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
