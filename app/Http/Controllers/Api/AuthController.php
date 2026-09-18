<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        return response()->json(['token' => $user->createToken('mobile')->plainTextToken, 'user' => $user]);
    }

    public function logout(Request $request) { $request->user()->currentAccessToken()?->delete(); return response()->json(['message' => 'Logged out']); }
}
