<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Used by route applications.invitees.change-chairs to alter the assigned chair count from the user dasbhoard.
 */
class ChangePhysicalChairsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "change_by" => "required|int",
        ];
    }
}
