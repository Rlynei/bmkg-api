<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
             User::select('id', 'name', 'username', 'email', 'role', 'is_active', 'created_at')
                ->orderBy('name')
                ->get()
        );
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'editor'])],
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => ['sometimes', 'required', Rule::in(['superadmin', 'admin', 'editor'])],
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Kalau dinonaktifkan, langsung cabut semua token aktif user itu (paksa logout)
        if (array_key_exists('is_active', $validated) && !$validated['is_active']) {
            $user->tokens()->delete();
        }
        $user->update($validated);
        return response()->json($user);
    }
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id){
            return response()->json(['message' => 'Cannot delete own account.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }
}
