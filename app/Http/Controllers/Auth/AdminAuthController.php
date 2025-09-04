<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Handle admin login request.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::with('roles')->where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email or password',
            ], 401);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        // Create Sanctum token
        $token = $user->createToken('admin-token')->plainTextToken;

        // Use the direct role column, fallback to relationship
        $primaryRole = $user->role ?? ($user->getRoleNames()[0] ?? null);
        $roleNames = $user->role ? [$user->role] : $user->getRoleNames();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'admin' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $primaryRole, // Primary role for backward compatibility
                'roles' => $roleNames,  // All roles array
                'last_login' => $user->last_login,
                'createdAt' => $user->created_at->toISOString(),
                'updatedAt' => $user->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated admin information.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles');
        // Use the direct role column, fallback to relationship
        $primaryRole = $user->role ?? ($user->getRoleNames()[0] ?? null);
        $roleNames = $user->role ? [$user->role] : $user->getRoleNames();

        return response()->json([
            'success' => true,
            'admin' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $primaryRole,
                'roles' => $roleNames,
                'last_login' => $user->last_login,
                'createdAt' => $user->created_at->toISOString(),
                'updatedAt' => $user->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * Verify token and return user info.
     */
    public function verify(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or inactive user',
            ], 401);
        }

        $user->load('roles');
        // Use the direct role column, fallback to relationship
        $primaryRole = $user->role ?? ($user->getRoleNames()[0] ?? null);
        $roleNames = $user->role ? [$user->role] : $user->getRoleNames();

        return response()->json([
            'success' => true,
            'admin' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'role' => $primaryRole,
                'roles' => $roleNames,
                'last_login' => $user->last_login,
                'createdAt' => $user->created_at->toISOString(),
                'updatedAt' => $user->updated_at->toISOString(),
            ],
        ]);
    }
}
