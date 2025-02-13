<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
    protected $errorBag = 'check-out';

    public function rules(): array
    {
        return [
            "application" => [
                "string",
            ],
            "table_clean" => [
                "accepted",
            ],
            "waste_disposed" => [
                "accepted",
            ],
            "floor_undamaged" => [
                "accepted",
            ],
            "materials_removed" => [
                "accepted",
            ],
            "power_strip" => [
                "accepted",
            ],
            "ci_comment" => [
                "max:" . Comment::MAX_LENGTH,
            ],
        ];
    }
}
