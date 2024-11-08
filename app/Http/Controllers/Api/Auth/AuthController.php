<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  public function register(RegisterRequest $request)
  {
    $payload = $request->validated();

    $user = User::create($payload);
    $token = $user->createToken($request->name);

    return response()->json([
      'user' => $user,
      'token' => $token->plainTextToken
    ]);
  }

  public function login(Request $request)
  {
    return 'Login';
  }

  public function logout(Request $request)
  {
    return 'Logout';
  }
}
