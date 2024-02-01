@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header')
        @endcomponent
    @endslot

    {{-- Body --}}
{{ $slot }}

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
Any help required? Shoot us an email to: dealers@eurofurence.org

Eurofurence e.V. - Am Kielshof 21a - 51105 Köln
Vereinsregister AG Köln, Nr. 19784
1. Vorsitzender: Sven Tegethoff

Legal information according to §5 TMG obtainable at http://www.eurofurence.de/index.php?impressum
        @endcomponent
    @endslot
@endcomponent
