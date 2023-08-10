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

class CommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "comment" => [
                "min:1",
                "max:4096",
            ],
            "application" => [
                "integer",
            ],
            "admin_only" => [
                "accepted",
            ],
        ];
    }
}
