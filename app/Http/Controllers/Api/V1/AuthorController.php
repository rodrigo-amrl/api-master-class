<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Models\User;
use App\Http\Resources\Api\V1\UserResource;

class AuthorController extends ApiController
{
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }
    public function show(User $author)
    {
        if ($this->include('tickets'))
            return new UserResource($author->load('tickets'));
        return new UserResource($author);
    }
}
