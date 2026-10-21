<?php

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageBank', $this->route('tenant')) ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['required', 'in:verified,rejected']];
    }
}
