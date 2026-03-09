<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json(['errors' => [
        'email' => ['These credentials do not match our records.']
        ]
      ], 401);
    }

    $token = $user->createToken(name: config('sanctum.token_name'))->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'user' => $user,
    ],200);
  }
  public function register(Request $request){
    $data = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => [
        'required',
        'string',
        'min:8',
        'regex:/[A-Z]/',
        'regex:/[a-z]/',
        'regex:/[0-9]/',
        'regex:/[!@#$%^&*]/',
        'regex:/^[A-Za-z0-9!@#$%^&*]+$/',
        'confirmed',
        ],
    ]);

    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => bcrypt($data['password']),
    ]);

    $token = $user->createToken(name: config('sanctum.token_name'))->plainTextToken;

    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
    ], 201);
  }

  public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out'], 200);
  }
}
