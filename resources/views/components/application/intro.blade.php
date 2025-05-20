@props(['applicationType'])
@if ($applicationType === \App\Enums\ApplicationType::Dealer || $applicationType === \App\Enums\ApplicationType::Share)
    @if (Carbon\Carbon::parse(config('convention.reg_end_date'))->isFuture())
        <p>
            Please fill out the form below to apply as {{ $applicationType->value }} at the Dealers’ Den. You may
            edit your application at any time before the deadline of
            <b>{{ Carbon\Carbon::parse(config('convention.reg_end_date'))->format('d.m.Y H:i') }}</b>.
            Please read the help texts carefully, and enter your information to the best of your ability.
        </p>
        <p>
            The Dealers’ Den Management will review your submission after the registration period has ended. Status
            emails about approval, denial, or being put on the waiting list will be sent after the review. Upon
            approval, your Eurofurence registration will be updated with a dealership package. The package will show
            up as an extra amount due in your registration. Payment is handled through the Eurofurence Registration
            website.
        </p>
    @else
        <p>
            The registration period has ended. You can still update your profile data which will be displayed in the
            EF app.
        </p>
    @endif
@else
    <p>
        Please fill out the form below to apply as {{ $applicationType->value }} at the Dealers’ Den.
        Please read the help texts carefully, and enter your information to the best of your ability.
    </p>
@endif
