<?php

namespace App\Events;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class ChildRemovedEvent
{
    use Dispatchable;

    public Application $child;

    public function __construct(Application $child)
    {
        $this->child = $child;
    }
}
