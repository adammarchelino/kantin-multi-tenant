<?php

namespace App\Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTenantRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('assignRole', $this->route('tenant')) ?? false;
    }

    public function rules(): array
    {
        return ['email' => ['required', 'email'], 'role' => ['required', 'in:owner,manager,operator,cashier,finance,viewer']];
    }
}
