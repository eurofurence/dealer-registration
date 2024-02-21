<?php

namespace App\Http\Requests;

use App\Enums\ApplicationType;
use App\Http\Controllers\ProfileController;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class ApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        /**
         * This is a multi request, it is accessed in the following circumstances
         * User Creates Application
         * User Updates Application
         * User joins another Application as share/assistant (code required)
         * User updates another Application as share (code not required aslong user does not change role)
         */

        $appValidations = [
            "applicationType" => [
                Rule::enum(ApplicationType::class),
            ],
            "code" => [
                new RequiredIf($this->isCodeRequired()),
            ],
            "displayName" => [
                "nullable",
                "exclude_if:applicationType,assistant",
                "max:255",
            ],
            "website" => [
                "nullable",
                "exclude_if:applicationType,assistant",
                "max:255",
            ],
            "merchandise" => [
                "exclude_if:applicationType,assistant",
                "required_unless:applicationType,assistant",
                "min:3",
                "max:4096",
            ],
            "denType" => [
                "required_if:applicationType,dealer",
                "exclude_unless:applicationType,dealer",
            ],
            "space" => [
                "required_if:applicationType,dealer",
                "exclude_unless:applicationType,dealer",
                "int",
                "exists:table_types,id",
            ],
            "wanted" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
                "max:4096",
            ],
            "wallseat" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "power" => [
                "exclude_unless:applicationType,dealer",
                "nullable",
            ],
            "additionalSpaceRequestText" => [
                "exclude_unless:applicationType,dealer",
                "required_if_accepted:additionalSpaceRequest",
                "max:4096",
            ],
            "comment" => [
                "nullable",
                "max:4096",
            ],
            "tos" => [
                new RequiredIf($this->routeIs('applications.store')),
            ],
        ];

        $profileValidations = ProfileController::getValidations();

        if (Carbon::parse(config('con.reg_end_date'))->isFuture()) {
            return array_merge($appValidations, $profileValidations);
        } else {
            return $profileValidations;
        }
    }

    private function isCodeRequired(): bool
    {
        $isRequestForFullDealership = ApplicationType::tryFrom($this->get('applicationType')) === ApplicationType::Dealer;
        // Case 1: User does not have any Application and applies for full dalership -> No Code Required
        if ($isRequestForFullDealership) {
            return false;
        }

        $userApplication = Auth::user()->application;
        // Case 2:
        // wishes to update their application to the same type
        $userDoesNotChangeType = $userApplication?->type === ApplicationType::tryFrom($this->get('applicationType'));
        // User is already in another dealership
        $alreadyPartOfDealership = $userApplication?->parent !== null;
        // User did not supply a new code
        if ($alreadyPartOfDealership && $userDoesNotChangeType) {
            return false;
        }

        return true;
    }

    public function authorize(): bool
    {
        $application = Auth::user()->application;
        if ($this->routeIs('applications.store')) {
            $newApplicationType = Application::determineApplicationTypeByCode($this->get('code'));
            // Only allow access to applications.store route if no active application exists or type changes
            // while reg is still open …
            if (Carbon::parse(config('con.reg_end_date'))->isFuture()) {
                return is_null($application) || !$application->isActive() || $newApplicationType !== $application->type;
            }
            // … or somebody wants to become an assistant …
            else {
                return $newApplicationType === ApplicationType::Assistant && (is_null($application) || !$application->isActive() || $newApplicationType !== $application->type);
            }
        }
        // … otherwise require existing and active application for this request.
        return !is_null($application) && $application->isActive();
    }
}
