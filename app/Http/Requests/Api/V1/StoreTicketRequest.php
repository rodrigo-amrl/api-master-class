<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

/**
 * @bodyParam data.attributes.title string required  The ticket's title. No-example
 * @bodyParam data.relationships.author.data.id integer required  The author id
 */
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
        $isTicketsController = $this->routeIs('tickets.store');
        $authorIdAttr = $isTicketsController ? 'data.relationships.author.data.id' : 'author';
        $authorRule = "required|integer|exists:users,id";
        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X'

        ];
        if ($isTicketsController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|array';
            $rules['data.relationships.author.data'] = 'required|array';
        }
        $rules[$authorIdAttr] = $authorRule . '|size' . Auth::user()->id;

        if (Auth::user()->tokenCan(Abilities::CreateOwnTicket))
            $rules['data.relationships.author.data.id'] = $authorRule;

        return $rules;
    }
    protected function prepareForValidation()
    {

        if ($this->routeIs('author.tickets.store'))
            $this->merge(['data.relationships.author.data.id' => $this->route('autor')]);
    }
    public function bodyParameters()
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => 'The ticket\'s title.',
                'example' => 'Ticket title'
            ],
            'data.attributes.description' => [
                'description' => 'The ticket\'s description.',
                'example' => 'Ticket description'
            ],
            'data.attributes.status' => [
                'description' => 'The ticket\'s status.',
                'example' => 'A'
            ],
            'data.relationships.author.data.id' => [
                'description' => 'The author id.',
                'example' => 1
            ]
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id']['description'] = 'The author assignet to the ticket';
        }
        return $documentation;
    }
}
