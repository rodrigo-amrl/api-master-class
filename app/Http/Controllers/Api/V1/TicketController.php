<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    use ApiResponses;

    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }
    public function store(StoreTicketRequest $request)
    {
        try {
            User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException $e) {
            return $this->ok('User not Found', [
                'error' => "The provided user id does not exists"
            ]);
        }
        return TicketResource::make(Ticket::create($request->mappedAttributes()));
    }
    public function show(int $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            if ($this->include('author')) {
                return new TicketResource($ticket->load('user'));
            }
            return new TicketResource($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
    public function update(UpdateTicketRequest $request, int $ticket_id)
    {

        try {
            $ticket = Ticket::findOrFail($ticket_id);


            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
    public function replace(ReplaceTicketRequest $request, int $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
    public function destroy(int $ticket_id)
    {

        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();
            return $this->ok('Ticket deleted');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
}
