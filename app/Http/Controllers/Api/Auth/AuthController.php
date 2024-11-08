<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function register(RegisterRequest $request)
  {
    $payload = $request->validated();

    $user = User::create($payload);
    $token = $user->createToken($payload['name']);

    return response()->json([
      'user' => $user,
      'token' => $token->plainTextToken
    ]);
  }

  public function login(LoginRequest $request)
  {
    $payload = $request->validated();

    $user = User::where('email', $payload['email'])->first();

    if (!$user || !Hash::check($payload['password'], $user->password)):
      return response()->json([
        'errors' => [
          'email' => ['The provided credentials are incorrect.']
        ]
      ]);
    endif;

    $token = $user->createToken($user->name);

    return response()->json([
      'user' => $user,
      'token' => $token->plainTextToken
    ]);
  }

  public function logout(Request $request)
  {
    return 'Logout';
  }
}
