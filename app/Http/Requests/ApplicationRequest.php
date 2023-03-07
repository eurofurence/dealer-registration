<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\Profile;
use App\Models\TableType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\ExcludeIf;
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
        $newApplicationType = Application::determineApplicationTypeByCode($this->get('code'));
        $parentApplication = Application::findByCode($this->get('code'));
        $application = \Auth::user()->application;
        return [
            "applicationType" => [
                Rule::enum(ApplicationType::class),
            ],
            "code" => [
                new RequiredIf($this->isCodeRequired()),
            ],
            "displayName" => [
                "nullable",
                "exclude_if:applicationType,assistant",
            ],
            "website" => [
                "nullable",
                "exclude_if:applicationType,assistant",
                "url",
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
            "tos" => [
                new RequiredIf($this->routeIs('applications.store')),
            ],
        ];
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
            return is_null($application) || $application->getStatus() === ApplicationStatus::Canceled;
        }
        return !is_null($application) && $application->getStatus() !== ApplicationStatus::Canceled;
    }

    public function act()
    {
        // Determine Application Type
        $newApplicationType = Application::determineApplicationTypeByCode($this->get('code'));

        $application = \Auth::user()->application;

        // If an the application is not for a dealer
        if ($newApplicationType !== ApplicationType::Dealer) {
            $newParent = Application::findByCode($this->get('code'))->id;
            $parentId = $newParent ?? $application->parent;
            return $this->update($newApplicationType, $parentId);
        }

        return $this->update($newApplicationType);
    }

    public function update(ApplicationType $applicationType, int|null $parentId = null)
    {
        $result = Application::updateOrCreate([
            "user_id" => \Auth::id(),
        ], [
            "table_type_requested" => $this->get('space'),
            "type" => $applicationType,
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
            "waiting_at" => null,
            "offer_sent_at" => null,
            "offer_accepted_at" => null,
            "canceled_at" => null,
            "table_number" => null,
            "parent" => $parentId,
           ]);

        $imgThumbnailName = NULL;
        $imgArtistName = NULL;
        $imgArtName = NULL;
        $application_id = $result->id;

        if($this->hasFile('image_thumbnail')){
            $imgThumbnailName = 'thumbnail_'.$application_id.'.'.$this->file('image_thumbnail')->getClientOriginalExtension();
            $this->file('image_thumbnail')->move(public_path('images/upload'), $imgThumbnailName);
        }
        if($this->hasFile('image_artist')){
            $imgArtistName = 'artist_'.$application_id.'.'.$this->file('image_artist')->getClientOriginalExtension();
            $this->file('image_artist')->move(public_path('images/upload'), $imgArtistName);
        }
        if($this->hasFile('image_art')){
            $imgArtName = 'art_'.$application_id.'.'.$this->file('image_art')->getClientOriginalExtension();
            $this->file('image_art')->move(public_path('images/upload'), $imgArtName);
        }

        Profile::updateOrCreate([
           "application_id" => $application_id,
        ], [
            "short_desc" => $this->get('short_desc'),
            "artist_desc" => $this->get('artist_desc'),
            "art_desc" => $this->get('art_desc'),
            "website" => $this->get('profile_website'),
            "twitter" => $this->get('twitter'),
            "telegram" => $this->get('telegram'),
            "discord" => $this->get('discord'),
            "tweet" => $this->get('tweet'),
            "art_preview_caption" => $this->get('art_preview_caption'),
            "is_print" => $this->get('is_print') === "on",
            "is_artwork" => $this->get('is_artwork') === "on",
            "is_fursuit" => $this->get('is_fursuit') === "on",
            "is_commissions" => $this->get('is_commissions') === "on",
            "is_misc" => $this->get('is_misc') === "on",
            "attends_thu" => $this->get('attends_thu') === "on",
            "attends_fri" => $this->get('attends_fri') === "on",
            "attends_sat" => $this->get('attends_sat') === "on",
            "image_thumbnail" => $imgThumbnailName,
            "image_artist" => $imgArtistName,
            "image_art" => $imgArtName,
           ]);

        return $result;
    }
}
