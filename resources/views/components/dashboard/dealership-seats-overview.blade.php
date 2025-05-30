@props(['seats', 'chairControls' => false])

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
    <div class="mx-auto text-center mt-1">
        @for($i = 0; $i < $seats['physical_chairs']; $i++)
            <span class="badge text-bg-warning">Chair</span>
        @endfor
        @if($seats['physical_chairs'] <= 0)
                <span class="badge text-bg-warning text-danger-emphasis">You chose to get <b>no physical chairs</b>!</span>
        @endif
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
    @if($chairControls)
        <div class=" alert alert-info mx-auto text-center mt-3 py-2">
            <p>
                <b class="badge text-bg-warning">NEW:</b>
                Please tell us how many <b>physical chairs</b> (stools to sit on) you really need.
                This helps us to set up the Dealers' Den most efficiently.
            </p>
            <p class="h5 text-dark">
                You will get <b>{{ $seats['physical_chairs'] }}</b>
                {{ $seats['physical_chairs'] == 1 ? 'chair' : 'chairs' }}.
            </p>
            <form method="POST" action="{{ route('applications.invitees.change-chairs') }}">
                @csrf
                <button name="change_by" value="1" class="btn btn-sm btn-outline-primary" @disabled($seats['physical_chairs'] >= $seats['table'])>Add chair</button>
                <button name="change_by" value="-1" class="btn btn-sm btn-outline-danger" @disabled($seats['physical_chairs'] <= 0)>Remove chair</button>
            </form>
            @if(Session::exists('physical-chair-change'))
                @php($pcc = Session::get('physical-chair-change'))
                <p class="text-{{ $pcc['success'] ? 'success' : 'danger'  }}-emphasis p-2 mt-2 bg-dark-subtle">
                    {{ $pcc['message'] }}
                </p>
            @endif
        </div>
    @endif
</div>
