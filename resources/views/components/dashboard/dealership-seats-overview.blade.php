@props(['seats'])

<div class="border rounded mx-auto border-dark-subtle bg-light p-3 mb-3">
    <div class="mx-auto text-center mb-1">
        @for ($i = 0; $i < $seats['dealers']; $i++)
            <span
                class="btn btn-primary dd-table-button cursor-default @if ($i >= $seats['table']) border-danger text-danger @endif"
                title="Dealer">D</span>
        @endfor
        @for ($i = 0; $i < $seats['free']; $i++)
            <span class="btn btn-secondary dd-table-button cursor-default" title="Free">F</span>
        @endfor
        @if (!is_null($seats['additional']))
            <span class="btn btn-dark dd-table-button cursor-default"
                    title="Free ({{ ucfirst($seats['additional']) }})">F{{ ucfirst(substr($seats['additional'], 0, 1)) }}</span>
        @endif
        @for ($i = 0; $i < $seats['assistants']; $i++)
            <span
                class="btn btn-info dd-table-button cursor-default @if ($i >= max($seats['table'] - $seats['dealers'], 1)) border-danger text-danger @endif"
                title="Assistant">A</span>
        @endfor
    </div>
    <div class="mx-auto text-center mt-3">
        <i>Legend:</i>
        <span class="badge text-bg-primary">Dealer</span>
        <span class="badge text-bg-secondary">Free</span>
        @if (!is_null($seats['additional']))
            <span class="badge text-bg-dark">Free ({{ ucfirst($seats['additional']) }})</span>
        @endif
        <span class="badge text-bg-info">Assistant</span>
    </div>
    @if ($seats['free'] < 0)
        <div class="alert alert-danger text-center fw-bold mt-3 mb-0">
            You have too many people in your dealership for the number of
            seats available for your table size.<br />
            Please remove excess shares or assistants.
        </div>
    @endif
</div>
