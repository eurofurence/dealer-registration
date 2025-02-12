<?php

namespace App\Http\Requests;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InviteeRemovalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "invitee_id" => "required|string"
        ];
    }

    public function authorize(): bool
    {
        $inviteeApplication = Application::findOrFail($this->get('invitee_id'));
        return  Auth::user()->application->id === $inviteeApplication->parent_id;
    }
}
