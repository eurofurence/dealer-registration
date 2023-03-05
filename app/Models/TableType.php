<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableType extends Model
{
    use HasFactory;
    public function application()
    {
        $this->belongsTo(Application::class);
    }
}
