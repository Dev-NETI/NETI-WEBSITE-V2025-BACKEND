<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $users = User::with('roles')->orderBy('created_at', 'desc')->get();

            $formattedUsers = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'role' => $user->getRoleNames()[0] ?? null, // For backward compatibility
                    'is_active' => $user->is_active,
                    'isActive' => $user->is_active,
                    'last_login' => $user->last_login,
                    'lastLogin' => $user->last_login,
                    'created_at' => $user->created_at,
                    'createdAt' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at,
                    'updatedAt' => $user->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'users' => $formattedUsers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'roles' => 'required|array|min:1',
                'roles.*' => 'exists:roles,name'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
            ]);

            // Assign roles
            $user->syncRoles($request->roles);

            // Return the created user with roles
            $user->load('roles');

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'role' => $user->getRoleNames()[0] ?? null,
                    'is_active' => $user->is_active,
                    'isActive' => $user->is_active,
                    'last_login' => $user->last_login,
                    'lastLogin' => $user->last_login,
                    'created_at' => $user->created_at,
                    'createdAt' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at,
                    'updatedAt' => $user->updated_at->toISOString(),
                ],
                'message' => 'User created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with('roles')->findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'role' => $user->getRoleNames()[0] ?? null,
                    'is_active' => $user->is_active,
                    'isActive' => $user->is_active,
                    'last_login' => $user->last_login,
                    'lastLogin' => $user->last_login,
                    'created_at' => $user->created_at,
                    'createdAt' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at,
                    'updatedAt' => $user->updated_at->toISOString(),
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'sometimes|string|min:6',
                'roles' => 'sometimes|array|min:1',
                'roles.*' => 'exists:roles,name',
                'is_active' => 'sometimes|boolean',
                'isActive' => 'sometimes|boolean' // For backward compatibility
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->has('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            } elseif ($request->has('isActive')) {
                $updateData['is_active'] = $request->isActive;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            // Update roles if provided
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            }

            // Return the updated user with roles
            $user->load('roles');

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'role' => $user->getRoleNames()[0] ?? null,
                    'is_active' => $user->is_active,
                    'isActive' => $user->is_active,
                    'last_login' => $user->last_login,
                    'lastLogin' => $user->last_login,
                    'created_at' => $user->created_at,
                    'createdAt' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at,
                    'updatedAt' => $user->updated_at->toISOString(),
                ],
                'message' => 'User updated successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Soft delete by setting is_active to false
            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::active()->get();

            $formattedRoles = $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'is_active' => $role->is_active,
                ];
            });

            return response()->json([
                'success' => true,
                'roles' => $formattedRoles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'is_active' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->update(['is_active' => $request->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'isActive' => $user->is_active,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update user status: ' . $e->getMessage()
            ], 500);
        }
    }
}
