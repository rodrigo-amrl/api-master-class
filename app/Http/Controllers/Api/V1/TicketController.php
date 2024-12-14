<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Models\Ticket;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiResponses;

class TicketController extends ApiController
{
    use ApiResponses;
    protected $policyClass = TicketPolicy::class;
    /**
     * Get All Tickets
     *
     * @group Managing Tickets
     * @queryParam sort string Data fild(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }
    /**
     * Create a tickets
     * 
     * Creates a new ticket. Users can only create tickets for themselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     */
    public function store(StoreTicketRequest $request)
    {
        if ($this->isAble('store', Ticket::class))
            return TicketResource::make(Ticket::create($request->mappedAttributes()));

        return $this->notAuthorized('You are not authorized to update this ticket', 401);
    }
    public function show(Ticket $ticket)
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }
        return new TicketResource($ticket);
    }
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {

        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->notAuthorized('You are not authorized to update this ticket', 401);
    }
    public function replace(ReplaceTicketRequest $request, Ticket $ticket)
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->notAuthorized('You are not authorized to update this ticket', 401);
    }
    public function destroy(Ticket $ticket)
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket deleted');
        }
        return $this->notAuthorized('You are not authorized to update this ticket', 401);
    }
}
