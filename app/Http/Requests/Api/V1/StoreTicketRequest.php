<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $authorIdAttr = $this->routeIs('tickets.store') ? 'data.relationships.author.data.id' : 'author';
        $authorRule = "required|integer|exists:users,id";
        $rules = [
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            $authorIdAttr => $authorRule . '|size' . Auth::user()->id,

        ];

        if (Auth::user()->tokenCan(Abilities::CreateOwnTicket))
            $rules['data.relationships.author.data.id'] = $authorRule;

        return $rules;
    }
    protected function prepareForValidation()
    {

        if ($this->routeIs('author.tickets.store'))
            $this->merge(['data.relationships.author.data.id' => $this->route('autor')]);
    }
}
