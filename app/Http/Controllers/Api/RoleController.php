<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:mst_roles,code',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        try {
            $role = Role::create([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return response()->json(['message' => 'success', 'data' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('size', 10);
        $page = $request->query('page', 1);
        $search = $request->query('search', '');

        try {
            $query = Role::query();
            if (!empty($search)) {
                $query->where('name', 'LIKE', "%$search%");
            }
            $roles = $query->orderBy('created_at', 'DESC')
                           ->paginate($limit, ['*'], 'page', $page);
            
            return response()->json(['message' => 'success', 'data' => $roles]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}