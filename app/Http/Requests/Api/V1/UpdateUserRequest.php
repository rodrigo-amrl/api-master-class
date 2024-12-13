<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends BaseUserRequest
{

    public function authorize(): bool
    {
        return false;
    }
    public function rules(): array
    {
        $rules = [
            'data.attributes.name' => 'sometimes|string',
            'data.attributes.email' => 'sometimes|string',
            'data.attributes.isManager' => 'sometimes|boolean',
            'data.attributes.password' => 'sometimes|string',
        ];
        return $rules;
    }
}
