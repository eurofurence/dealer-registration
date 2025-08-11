<?php

namespace App\View\Components\Application;

use App\DTOs\ProfileCompletionStep;
use App\Models\Application;
use App\Services\ProfileCompletionEvaluator;
use Illuminate\View\Component;

class CompletionProgress extends Component
{

    public ProfileCompletionEvaluator $completion;

    public function __construct(public Application $application)
    {
       $this->completion = new ProfileCompletionEvaluator($this->application);
       uasort($this->completion->steps, fn(ProfileCompletionStep $a, ProfileCompletionStep $b) => $b->weight <=> $a->weight);
    }

    public function render()
    {
        return view('components.application.completion-progress');
    }
}
