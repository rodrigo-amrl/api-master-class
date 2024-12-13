<?php

namespace App\Http\Requests\Api\V1;;

class StoreUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return false;
    }
    public function rules(): array
    {
        $rules = [
            'data.attributes.name' => 'required|string',
            'data.attributes.email' => 'required|string',
            'data.attributes.isManager' => 'required|boolean',
            'data.attributes.password' => 'required|string',
        ];
        return $rules;
    }
}
