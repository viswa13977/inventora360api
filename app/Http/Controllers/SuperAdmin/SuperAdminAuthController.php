<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin;

class SuperAdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('super_admin')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('super_admin')->factory()->getTTL() * 60,
        ]);
    }

    public function logout()
    {
        Auth::guard('super_admin')->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile()
    {
        return response()->json(Auth::guard('super_admin')->user());
    }

    public function refresh()
    {
        return response()->json([
            'access_token' => Auth::guard('super_admin')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('super_admin')->factory()->getTTL() * 60,
        ]);
    }
}
