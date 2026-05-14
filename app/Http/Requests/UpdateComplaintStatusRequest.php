<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateComplaintStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_status'       => ['required', 'string', 'in:pending_review,validated,in_progress,resolved,closed,rejected'],
            'comment'          => ['nullable', 'string', 'max:1000'],
            'rejection_reason' => ['required_if:new_status,rejected', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.required_if' => 'A rejection reason is required when rejecting a complaint.',
        ];
    }
}
