<?php

namespace App\Events;

use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class ApplicationUpdatedEvent
{
    use Dispatchable;

    public Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }
}
