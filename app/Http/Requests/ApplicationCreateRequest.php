<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\TableType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "applicationType" => [
                Rule::enum(ApplicationType::class),
            ],
            "invitationCode" => [
                "required_unless:applicationType,dealer",
            ],
            "displayName" => [
                "nullable",
                "exclude_if:applicationType,assistant",
            ],
            "website" => [
                "nullable",
                "exclude_if:applicationType,assistant",
            ],
            "merchandise" => [
                "exclude_if:applicationType,assistant",
                "required_unless:applicationType,assistant",
                "min:3",
            ],
            "denType" => [
                "required_if:applicationType,dealer",
                "exclude_unless:applicationType,dealer",
            ],
            "mature" => [
                "nullable",
                "exclude_if:applicationType,assistant",
            ],
            "space" => [
                "required_if:applicationType,dealer",
                "exclude_unless:applicationType,dealer",
                "int",
                "exists:table_types,id",
            ],
            "wallseat" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "power" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "wanted" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "unwanted" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "comment" => "nullable",
            "tos" => "required",
        ];
    }

    public function authorize(): bool
    {
        $application = \Auth::user()->applications;
        return is_null($application) || $application->getStatus() === ApplicationStatus::Canceled;
    }

    public function store()
    {
        Application::updateOrCreate([
            "user_id" => \Auth::id()
        ],[
            "table_type_requested" => $this->get('space'),
            "type" => ApplicationType::Dealer,
            "display_name" => $this->get('displayName'),
            "website" => $this->get('website'),
            "merchandise" => $this->get('merchandise'),
            "is_afterdark" => $this->get('denType') === "denTypeAfterDark",
            "is_mature" => $this->get('mature') === "on",
            "is_power" => $this->get('power') === "on",
            "is_wallseat" => $this->get('wallseat') === "on",
            "wanted_neighbors" => $this->get('wanted'),
            "unwanted_neighbors" => $this->get('unwanted'),
            "comment" => $this->get('comment'),
            "canceled_at" => null,
            "accepted_at" => null,
            "allocated_at" => null,
            "table_number" => null
        ]);
    }
}
