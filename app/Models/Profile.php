<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public function application()
    {
        $this->belongsTo(Application::class);
    }
}
