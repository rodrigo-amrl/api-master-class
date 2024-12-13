<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Models\User;
use App\Http\Resources\Api\V1\UserResource;
use App\Policies\V1\UserPolicy;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends ApiController
{
    use ApiResponses;

    protected $policyClass = UserPolicy::class;
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }
    public function show(User $user)
    {
        if ($this->include('tickets'))
            return new UserResource($user->load('tickets'));
        return new UserResource($user);
    }
    public function store(StoreUserRequest $request)
    {
        try {
            $this->isAble('store', User::class);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 401);
        }
        return UserResource::make(User::create($request->mappedAttributes()));
    }
    public function update(StoreUserRequest $request, int $user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $this->isAble('update', $user);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 401);
        }
        $user->update($request->mappedAttributes());
        return new UserResource($user);
    }
    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('replace', $user);
            $user->update($request->mappedAttributes());
            return new UserResource($user);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this ticket', 401);
        }
    }
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $this->isAble('delete', $user);
            $user->delete();
            return $this->success('User deleted successfully');
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this ticket', 401);
        }
    }
}
