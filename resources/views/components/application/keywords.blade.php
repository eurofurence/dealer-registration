@props(['categories', 'keywordIds'])
<div class="accordion" id="keywords">
    @foreach ($categories as $category)
        @php(
    $hasKeywordsFromCategory = !empty(
        array_intersect(
            old('_token') ? old('keywords') ?? [] : $keywordIds,
            $category->keywords()->pluck('id')->toArray(),
        )
    )
)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button{{ $hasKeywordsFromCategory ? '' : ' collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#category-{{ $loop->index }}"
                    aria-expanded="{{ $hasKeywordsFromCategory ? 'true' : 'false' }}"
                    aria-controls="category-{{ $loop->index }}">
                    {{ $category->name }}
                </button>
            </h2>
            <div id="category-{{ $loop->index }}"
                class="accordion-collapse collapse{{ $hasKeywordsFromCategory ? ' show' : '' }}">
                <div class="accordion-body">
                    @foreach ($category->keywords()->get() as $keyword)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="keywords[]"
                                value="{{ $keyword->id }}"
                                id="keyword-{{ $loop->parent->index }}-{{ $loop->index }}"
                                @checked(old('_token') ? in_array($keyword->id, old('keywords') ?? []) : in_array($keyword->id, $keywordIds))>
                            <label class="form-check-label"
                                for="keyword-{{ $loop->parent->index }}-{{ $loop->index }}">
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
