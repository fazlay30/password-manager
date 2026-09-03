<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $roles = Role::all();
            return response()->json(['success' => true, 'data' => $roles->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $role = Role::query()->where('id', $id)->first();
            if (!$role) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Role not found!']], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['success' => true, 'data' => $role->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $role = Role::query()->where('id', $id)->first();
            if (!$role) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Role not found!']], Response::HTTP_NOT_FOUND);
            }

            $role->update([
                'name' => $validated['name'],
            ]);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Role updated successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
