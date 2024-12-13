<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends ApiController
{
    use ApiResponses;
    protected $policyClass = TicketPolicy::class;
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }
    public function store(StoreTicketRequest $request)
    {
        try {
            $this->isAble('store', Ticket::class);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 401);
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

            $this->isAble('update', $ticket);
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 401);
        }
    }
    public function replace(ReplaceTicketRequest $request, int $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $this->isAble('replace', $ticket);
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
            $this->isAble('delete', $ticket);
            $ticket->delete();
            return $this->ok('Ticket deleted');
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
}
