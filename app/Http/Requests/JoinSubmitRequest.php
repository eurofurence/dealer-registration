<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinSubmitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "code" => "required|min:1|exists:applications,"
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
