<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;
    public $timestamps = true;
    protected $primaryKey = 'uuid';

    protected $guarded = [];

    public function keywords()
    {
        return $this->hasMany(Keyword::class)->orderBy('name', 'asc');
    }

    public function profiles()
    {
        return $this->hasManyThrough(Profile::class, Keyword::class);
    }
}
