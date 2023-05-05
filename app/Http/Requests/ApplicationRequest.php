<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
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

        if (Carbon::parse(config('ef.reg_end_date'))->isFuture()) {
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
        $application = \Auth::user()->application;
        if ($this->routeIs('applications.store')) {
            $newApplicationType = Application::determineApplicationTypeByCode($this->get('code'));
            // Only allow access to applications.store route if no active application exists or type changes
            // while reg is still open …
            if (Carbon::parse(config('ef.reg_end_date'))->isFuture()) {
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

    public function act()
    {
        $application = \Auth::user()->application;

        $newApplicationType = ($this->get('code')) ? Application::determineApplicationTypeByCode($this->get('code')) : $application->type ?? ApplicationType::Dealer;

        // If an the application is not for a dealer
        if ($newApplicationType !== ApplicationType::Dealer || ($application?->type !== ApplicationType::Dealer && $application?->canceled_at !== null)) {
            $parentId = Application::findByCode($this->get('code'))?->id ?? $application->parent;
            return $this->update($newApplicationType, $parentId);
        }

        return $this->update($newApplicationType);
    }

    private function update(ApplicationType $applicationType, int|null $parentId = null)
    {
        $application = null;
        // Only allow changes to applications while reg is still open
        if (Carbon::parse(config('ef.reg_end_date'))->isFuture() || $applicationType === ApplicationType::Assistant) {
            $application = Application::updateOrCreate([
                "user_id" => \Auth::id(),
            ], [
                "table_type_requested" => $this->get('space'),
                "type" => $applicationType,
                "display_name" => $this->get('displayName'),
                "website" => $this->get('website'),
                "merchandise" => $this->get('merchandise'),
                "is_afterdark" => $this->get('denType') === "denTypeAfterDark",
                "is_power" => $this->get('power') === "on",
                "is_wallseat" => $this->get('wallseat') === "on",
                "wanted_neighbors" => $this->get('wanted'),
                "comment" => $this->get('comment'),
                "waiting_at" => null,
                "offer_sent_at" => null,
                "offer_accepted_at" => null,
                "canceled_at" => null,
                "table_number" => null,
                "parent" => $parentId,
            ]);
        } else {
            $application = Application::findByUserId(\Auth::id());
        }
        if ($application && $application->isActive() && $applicationType !== ApplicationType::Assistant) {
            ProfileController::createOrUpdate($this, $application->id);
        }
    }
}
