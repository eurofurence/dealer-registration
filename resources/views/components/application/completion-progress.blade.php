<div class="accordion">
    <div class="accordion-item">
        <div class="accordion-header">
            <button type="button" class="accordion-button collapsed" data-bs-target="#collapseProfileCompletion"
                 data-bs-toggle="collapse" aria-expanded="true" aria-controls="collapseProfileCompletion">
                <span class="visually-hidden-focusable">Progress (click for details): </span>
                <div class="progress w-100 me-3" style="height: 2.5em;">
                    <div class="progress-bar{{ $completion->isCompleted() ? '' : ' text-bg-warning' }}" role="progressbar"
                         style="width: {{ $completion->weightedPercent }}%; font-weight: bold;"
                         aria-valuenow="{{ $completion->progress }}" aria-valuemin="0" aria-valuemax="{{ $completion->maxProgress }}"
                    >
                        {{ $completion->progress }} / {{ $completion->maxProgress }} ({{ $completion->weightedPercent }} %)
                    </div>
                </div>
            </button>
        </div>
        <div id="collapseProfileCompletion" class="accordion-collapse collapse">
            <div class="accordion-body">
                @foreach($completion->steps as $step)
                    <div class="row mb-3">
                        <div class="col-12 col-xl-4">
                            <span class="align-baseline" style="display: inline-grid; grid-template-columns: repeat(5,auto);" title="Impact: {{ $step->weight }}">
                                @for($i = 0; $i < $step->weight; $i++)<x-heroicon-s-star class="text-primary" width="0.75em" />@endfor
                                @for($i = $step->weight; $i < 5; $i++)<x-heroicon-o-star class="text-secondary" width="0.75em" />@endfor
                            </span>
                            <span class="fw-bold mx-2">{{ $step->name }}</span>
                            @if($step->isCompleted())
                                <span class="badge text-bg-primary float-end mt-1">Completed <x-heroicon-s-check-badge width="1em"/></span>
                            @else
                                @if($step->maxScore == 1 || $step->score == 0)
                                    <span class="badge text-bg-warning float-end mt-1">TODO <x-heroicon-s-exclamation-triangle width="1em"/></span>
                                @else
                                    <span class="badge text-bg-dark text-warning float-end mt-1">{{ $step->score }} / {{ $step->maxScore }} <x-heroicon-s-exclamation-triangle width="1em"/></span>
                                @endif
                            @endif
                        </div>
                        <div class="col-12 col-xl-8 text-dark-emphasis">
                            {{ $step->description }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
