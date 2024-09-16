@props(['categories'])
<div class="accordion" id="keywords">
    @foreach ($categories as $category)
        <div class="accordion-item">
            <h2 class="accordion-header d-flex align-items-center">
                <a class="flex-shrink-1 btn btn-outline-primary fs-3" href="?type=keyword&search=c::{{ $category->id }}">
                    🔎
                </a>
                <button class="accordion-button collapsed w-100 fs-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#category-{{ $loop->index }}" aria-expanded="false"
                    aria-controls="category-{{ $loop->index }}">
                    {{ $category->name }}
                </button>
            </h2>
            <div id="category-{{ $loop->index }}" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <div class="list-group">
                        @foreach ($category->keywords()->get() as $keyword)
                            <a class="list-group-item" href="?type=keyword&search=k::{{ $keyword->id }}">
                                🔎 {{ $keyword->name }}
                            </a>
                        @endforeach
                    </div>
                    <div class="form-text">
                        {{ $category->description }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
