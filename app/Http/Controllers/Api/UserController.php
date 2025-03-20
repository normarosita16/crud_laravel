<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:mst_roles,id',
            'fullname' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:mst_users,email',
            'username' => 'required|string|max:255|unique:mst_users,username',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user = User::create([
            'role_id' => $request->role_id,
            'fullname' => $request->fullname,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_active' => 1,
        ]);

        return response()->json(['message' => 'Success Create User', 'data' => $user], 201);
    }

    public function listUser(Request $request): JsonResponse
    {
        $limit = $request->query('size', 10);
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        $query = User::with('role:id,name,code')
            ->where(function ($q) use ($search) {
                if (!empty($search)) {
                    $q->where('fullname', 'LIKE', "%$search%")
                        ->orWhere('email', 'LIKE', "%$search%")
                        ->orWhereHas('role', function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%$search%");
                        });
                }
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json(['message' => 'Success', 'data' => $query]);
    }

    public function delete($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => "User with id $id not found"], 404);
        }
        $user->delete();

        return response()->json(['message' => "Success delete user with id $id"]);
    }

    public function updateUser(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => "User with id $id not found"], 404);
        }

        $user->update([
            'role_id' => $request->role_id ?? $user->role_id,
            'fullname' => $request->fullname ?? $user->fullname,
            'phone_number' => $request->phone_number ?? $user->phone_number,
            'email' => $request->email ?? $user->email,
            'username' => $request->username ?? $user->username,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'is_active' => $request->is_active ?? $user->is_active,
        ]);

        return response()->json(['message' => "Success updated user with id $id"]);
    }

    public function viewUser($id): JsonResponse
    {
        $user = User::with('role:id,name,code')->find($id);
        if (!$user) {
            return response()->json(['message' => "User with id $id not found"], 404);
        }
        return response()->json(['message' => 'Success', 'data' => $user]);
    }
}
