<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardInfo extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-info';
    protected int | string | array $columnSpan = 'full';
}
