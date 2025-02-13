<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    protected $errorBag = 'comment';

    public function rules(): array
    {
        return [
            "comment" => [
                "filled",
                "max:" . Comment::MAX_LENGTH,
            ],
            "application" => [
                "string",
            ],
        ];
    }
}
