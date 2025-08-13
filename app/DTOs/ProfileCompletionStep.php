<?php

namespace App\DTOs;


class ProfileCompletionStep
{
    /**
     * @var int The score the profile earned, 0 means "not completed at all", $maxScore is "completed".
     */
    public int $score;

    /**
     * @param string $id Unique short id string letters, numbers and dash only.
     * @param string $name Display name for this criterion.
     * @param string|null $description A short description of what this is.
     * @param int|bool $score May be boolean True=1 and False=0. The score the profile earned, 0 means "not completed at all", $maxScore is "completed".
     * @param int $maxScore The maximum score valued required to mark as completed, defaults to 1.
     * @param int $weight The weight for overall progress, 1 is the default, 5 should be maximum.
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null,
        int|bool $score = 0,
        public int $maxScore = 1,
        public int $weight = 1,
    )
    {
        if (is_numeric($score)) {
            $this->score = intval($score);
        } else {
            if ($this->maxScore === 1) {
                $this->score = $score ? 1 : 0;
            }
        }
    }

    /**
     * @return bool True if this step is fully completed, i.e. $score has reached $maxScore.
     */
    public function isCompleted(): bool
    {
        return $this->score >= $this->maxScore;
    }

}
