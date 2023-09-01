<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    protected $errorBag = 'check-in';

    public function rules(): array
    {
        return [
            "application" => [
                "integer",
            ],
            "waiver_signed" => [
                "accepted",
            ],
            "badge_received" => [
                "accepted",
            ],
            "ci_comment" => [
                "max:". Comment::MAX_LENGTH,
            ],
        ];
    }
}
