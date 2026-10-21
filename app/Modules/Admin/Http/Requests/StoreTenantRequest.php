<?php

namespace App\Modules\Admin\Http\Requests;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Tenant::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:120'], 'code' => ['required', 'alpha_dash', 'max:30'],
            'slug' => ['required', 'alpha_dash', 'max:100'], 'commission_rate' => ['required', 'numeric', 'between:0,100'],
            'pic_name' => ['required', 'string', 'max:120'], 'pic_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'bank_code' => ['required', 'string', 'max:20'], 'account_holder' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'regex:/^[0-9\s]{6,34}$/'],
        ];
    }
}
