@props(['categories', 'keywordUuids'])
@php($componentId = uniqid())

<div class="accordion" id="keywords-{{ $componentId }}">
    @foreach ($categories as $category)
        @php(
    $hasKeywordsFromCategory = !empty(
        array_intersect(
            $keywordUuids,
            $category->keywords()->pluck('uuid')->toArray(),
        )
    )
)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button{{ $hasKeywordsFromCategory ? '' : ' collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#category-{{ $componentId }}-{{ $loop->index }}"
                    aria-expanded="{{ $hasKeywordsFromCategory ? 'true' : 'false' }}"
                    aria-controls="category-{{ $componentId }}-{{ $loop->index }}">
                    {{ $category->name }}
                </button>
            </h2>
            <div id="category-{{ $componentId }}-{{ $loop->index }}"
                class="accordion-collapse collapse{{ $hasKeywordsFromCategory ? ' show' : '' }}">
                <div class="accordion-body">
                    @foreach ($category->keywords()->get() as $keyword)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="keywords[]"
                                value="{{ $keyword->uuid }}"
                                id="keyword-{{ $componentId }}-{{ $loop->parent->index }}-{{ $loop->index }}"
                                @checked(in_array($keyword->uuid, $keywordUuids))>
                            <label class="form-check-label"
                                for="keyword-{{ $componentId }}-{{ $loop->parent->index }}-{{ $loop->index }}">
                                {{ $keyword->name }}
                            </label>
                        </div>
                    @endforeach
                    <div class="form-text">
                        {{ $category->description }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
