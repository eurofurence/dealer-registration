<?php

namespace App\Services;

use App\DTOs\ProfileCompletionStep;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\Profile;

class ProfileCompletionEvaluator
{
    /**
     * @var ProfileCompletionStep[] Steps for this application.
     */
    public array $steps = [];

    public int $progress = 0;
    public int $maxProgress = 1;
    public int $weightedProgress = 0;
    public int $weightedMaxProgress = 1;
    public int $weightedPercent = 0;

    public function isCompleted() : bool
    {
        return $this->progress >= $this->maxProgress;
    }

    private function addStep(ProfileCompletionStep $step)
    {
        if (array_key_exists($step->id, $this->steps))
            throw new \LogicException("Duplicate profile completion step id '$step->id'");
        $this->steps[$step->id] = $step;
    }

    /**
     * @param string|null $str The string to test.
     * @return int Numbers of chars after trimming whitespace.
     */
    private static function string_has_chars(?string $str): int
    {
        return $str ? strlen(trim($str)) : 0;
    }

    protected function evaluate(Application $application)
    {
        // Shortcuts
        $isDealer = $application->type === ApplicationType::Dealer;
        $isShare = $application->type === ApplicationType::Share;
        $isAssistant = $application->type === ApplicationType::Assistant;
        /** @var Profile|null $profile */
        $profile = $application->profile;

        // Allow shares to share profile, in that case we display them a completed form (no leak information from main).
        if ($isShare && $profile?->is_hidden) {
            $this->addStep(new ProfileCompletionStep(
                'hidden_share', 'Share Profile',
                description: "You chose to hide your own profile, so you do not need to set anything. Please still make sure your main dealership profile is well filled!",
                score: true,
                weight: 0, // No score.
            ));
        }
        // Main Rules
        elseif ($isDealer || $isShare) {

            // "Thumbnail"
            $this->addStep(new ProfileCompletionStep(
                'image_thumbnail', 'Thumbnail Image',
                description: "Please provide at least this image: A picture to go along with your dealership name in lists and overviews.",
                score: self::string_has_chars($profile?->image_thumbnail) > 3,
                weight: 5, // This is VERY important! It is everywhere
            ));

            // Short Description
            $expectCharsAtLeast = 30;
            $this->addStep(new ProfileCompletionStep(
                'short_desc', 'Short Description',
                description: "Give your dealership an inviting short description of at least $expectCharsAtLeast characters.",
                score: self::string_has_chars($profile?->short_desc),
                maxScore: $expectCharsAtLeast,
                weight: 4, // This is important! It is in the profile header
            ));

            // "Profile Header"
            $this->addStep(new ProfileCompletionStep(
                'image_artist', 'Profile Image',
                description: "A profile picture to go along with your dealership description.",
                score: self::string_has_chars($profile?->image_artist) > 3,
                weight: 3, // This is VERY important! It is everywhere!
            ));

            // "Keywords"
            $expectCharsAtLeast = 1;
            $keywordCount = count($profile?->keywords ?? []);
            $this->addStep(new ProfileCompletionStep(
                'keywords', 'Set Keywords',
                description: "You should tick all keywords that match what you offer, so people find you.",
                score: $keywordCount,
                maxScore: $expectCharsAtLeast,
                weight: 4, // This is very important too!
            ));

            // "About the Artist"
            $expectCharsAtLeast = 50;
            $this->addStep(new ProfileCompletionStep(
                'artist_desc', 'About You',
                description: "Describe yourself in the \"About the Artist\" field with at least $expectCharsAtLeast characters.",
                score: self::string_has_chars($profile?->artist_desc),
                maxScore: $expectCharsAtLeast,
                weight: 2, // Also quite important
            ));

            // "Showcase Image"
            $hasShowcaseImage = self::string_has_chars($profile?->image_art) > 0;
            $hasShowcaseText = self::string_has_chars($profile?->art_preview_caption) > 5;
            $this->addStep(new ProfileCompletionStep(
                'showcase_image', 'Showcase Image',
                description: "Provide a Showcase Image with a Caption to present what you are offering.",
                score: $hasShowcaseImage ? ($hasShowcaseText ? 2 : 1) : 0,
                maxScore: 2,
                weight: 1,
            ));

            $expectCharsAtLeast = 50;
            $this->addStep(new ProfileCompletionStep(
                'art_desc', 'About your Art',
                description: "Describe in more detail what you are offering in the \"About the Art\" field with at least $expectCharsAtLeast characters.",
                score: self::string_has_chars($profile?->art_desc),
                maxScore: $expectCharsAtLeast,
                weight: 1,
            ));

            $this->addStep(new ProfileCompletionStep(
                'website', 'Link your Website',
                description: "If you have a website, please add it!",
                score: self::string_has_chars($profile?->website) > 3,
                weight: 3,
            ));

            $expectCharsAtLeast = 2;
            $linkCount = 0;
            if (self::string_has_chars($profile?->twitter) > 3) $linkCount++;
            if (self::string_has_chars($profile?->mastodon) > 3) $linkCount++;
            if (self::string_has_chars($profile?->bluesky) > 3) $linkCount++;
            if (self::string_has_chars($profile?->telegram) > 3) $linkCount++;
            if (self::string_has_chars($profile?->discord) > 3) $linkCount++;
            $this->addStep(new ProfileCompletionStep(
                'social_links', 'Link your Socials',
                description: "Give at least $expectCharsAtLeast ways to find you via social media links (other than your website).",
                score: $linkCount,
                maxScore: $expectCharsAtLeast,
                weight: 3,
            ));

            $expectCharsAtLeast = 50;
            $this->addStep(new ProfileCompletionStep(
                'tweet', 'Advertisement Text',
                description: "Write a short advertisement message of at least $expectCharsAtLeast characters.",
                score: self::string_has_chars($profile?->tweet),
                maxScore: $expectCharsAtLeast,
                weight: 1,
            ));
        }
    }

    /**
     * Evaluate the completion of a profile.
     *
     * @param Application|null $application The application to evaluate. Accepts null, yielding a single informative step.
     */
    public function __construct(?Application $application)
    {
        if (!$application) {
            $this->addStep(new ProfileCompletionStep(
                'create-application', 'Create Application', 0,
                'You need to begin an application before a progress can be shown.'
            ));
        } else {
            $this->evaluate($application);
            $maxProgress = 0;
            $progress = 0;
            $weightedMaxProgress = 0;
            $weightedProgress = 0;

            foreach ($this->steps as $step) {
                $maxScore = max(1, $step->maxScore);
                $score = max(0, min($step->score, $maxScore));
                $maxProgress++;
                $weight = max(1, $step->weight);
                $weightedMaxProgress += $weight;
                if ($score >= $maxScore) {
                    $progress += 1;
                    $weightedProgress += $weight;
                }
            }
            $this->maxProgress = floor($maxProgress);
            $this->weightedMaxProgress = floor($weightedMaxProgress);
            $this->weightedPercent = floor(100*$weightedProgress / max(1,$weightedMaxProgress));
            $this->progress = floor($progress);
            $this->weightedProgress = floor($weightedProgress);
        }
    }
}
