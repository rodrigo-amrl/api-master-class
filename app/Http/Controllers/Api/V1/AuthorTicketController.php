<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketController extends Controller
{
    use ApiResponses;
    public function index($author_id, TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }
    public function store($author_id, StoreTicketRequest $request)
    {
        try {
            User::findOrFail($author_id);
        } catch (ModelNotFoundException $e) {
            return $this->ok('User not Found', [
                'error' => "The provided user id does not exists"
            ]);
        }
        return TicketResource::make(Ticket::create($request->mappedAttributes()));
    }
    public function destroy(int $author_id, int $ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            if ($ticket->user_id == $author_id) {
                $ticket->delete();
                return $this->ok('Ticket deleted');
            }
            return $this->error('Ticket not Found', 404);
        } catch (ModelNotFoundException $e) {
            return $this->error('Ticket cannot be Found', 404);
        }
    }
}
