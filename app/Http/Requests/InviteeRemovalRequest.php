<?php

namespace App\Http\Requests;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;

class InviteeRemovalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "invitee_id" => "required|int"
        ];
    }

    public function authorize(): bool
    {
        $inviteeApplication = Application::findOrFail($this->get('invitee_id'));
        return  \Auth::user()->application->id === $inviteeApplication->parent;
    }
}
