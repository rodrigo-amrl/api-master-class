<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponses;
    public function login(LoginRequest $request)

    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password')))
            return $this->error('Invalid credential', 401);

        $user = User::firstWhere('email', $request->email);
        return $this->ok('Authenticated', [
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }
    public function register()
    {
        return $this->ok('register', '');
    }
}
