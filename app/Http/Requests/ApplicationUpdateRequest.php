<?php

namespace App\Http\Requests;

use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\TableType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "applicationType" => [
                Rule::enum(ApplicationType::class),
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
        ];
    }

    public function authorize(): bool
    {
        return \Auth::user()->applications()->count() === 1;
    }

    public function update()
    {
        $data = $this->validationData();
        \Auth::user()->applications->update([
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
        ]);
    }
}
