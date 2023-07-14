<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('react-app')->plainTextToken;
        return $this->respondCreated(['user' => UserResource::make($user), 'token' => $token]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::firstWhere('email', $data['email']);

        if (!$user) {
            return $this->respondNotFound("These credentials do not match our records.");

        } elseif (!Hash::check($data['password'], $user->password)) {
            return $this->respondUnAuthenticated("The provided password is incorrect.");
        }

        $token = $user->createToken('react-app')->plainTextToken;
        return $this->respondWithSuccess(['user' => UserResource::make($user), 'token' => $token]);
    }
}
