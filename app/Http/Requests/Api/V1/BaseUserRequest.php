<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseUserRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributesMap =   array_merge([
            'data.attributes.name' => 'name',
            'data.attributes.email' => 'email',
            'data.attributes.isManager' => 'is_manager',
            'data.attributes.password' => 'password',
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributesMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
                if ($attribute == 'password') {
                    $attributesToUpdate[$attribute] = bcrypt($this->input($key));
                }
            }
        }
        return $attributesToUpdate;
    }
    public function messages()
    {
        return [
            'data.attributes.status' => 'The data.attributes.status value is invalid. The status must be one of the following: A, C, H, X.',
        ];
    }
}
