@props(['applicant', 'application'])

<a href="{{ route('frontdesk', ['search' => $applicant->reg_id]) }}"
    @class([
        'form-control',
        'btn',
        'btn-primary',
        'fs-4',
        'btn-success' => $application->status === \App\Enums\ApplicationStatus::CheckedIn,
        'btn-warning' => $application->status === \App\Enums\ApplicationStatus::TableOffered ||
        $application->status === \App\Enums\ApplicationStatus::CheckedOut,
        'btn-danger' => $application->status !== \App\Enums\ApplicationStatus::CheckedIn && $application->status !== \App\Enums\ApplicationStatus::TableOffered &&
        $application->status !== \App\Enums\ApplicationStatus::CheckedOut,
        ])>
    {{ $applicant->name }} ({{$applicant->reg_id}}) – {{ $application->status->name }}</a>
