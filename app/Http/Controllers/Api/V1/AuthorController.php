<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;

class AuthorController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->include('tickets'))
            return UserResource::collection(User::with('tickets')->get());

        return UserResource::collection(User::all());
    }
    public function show(User $author)
    {
        if ($this->include('tickets'))
            return new UserResource($author->load('tickets'));
        return new UserResource($author);
    }
}
