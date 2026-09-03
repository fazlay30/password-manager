<?php

namespace App\Http\Controllers;

use App\Models\GroupProject;
use App\Models\GroupProjectCredential;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function show(Request $request) {
        return $request->user();
    }

    public function profileUpdate(Request $request): JsonResponse
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,' . Auth::id()],
//                'old_password' => ['required', 'string'],
//                'new_password' => ['nullable', Rules\Password::defaults()],
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $user = User::query()->where('id', Auth::id())->first();
            if (!$user) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'User not found!']], Response::HTTP_NOT_FOUND);
            }
//            if (!Hash::check($validated['old_password'], $user->password)) {
//                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Incorrect Password!']], Response::HTTP_NOT_ACCEPTABLE);
//            }

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'email_verified_at' => $user->email !== $validated['email'] ? null : $user->email_verified_at,
//                'password' => $validated['new_password'] !== null ? Hash::make($validated['new_password']) : $user->password,
            ]);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Profile updated successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => ['required', 'string'],
                'new_password' => ['required', Rules\Password::defaults()],
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $user = User::query()->where('id', Auth::id())->first();
            if (!$user) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'User not found!']], Response::HTTP_NOT_FOUND);
            }

            if ($user->password === null && $user->google_id !== null) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Google signed up user can not change password!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            if (!Hash::check($validated['old_password'], $user->password)) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Incorrect Password!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Password updated successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAccount(): JsonResponse
    {
        $user = User::query()->where('id', Auth::id())->first();
        if (!$user) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'User not found!']], Response::HTTP_NOT_FOUND);
        }

        UserCredential::query()->where('fk_user_id', Auth::id())->delete();
        $groupProjects = GroupProject::query()->where('fk_user_id', Auth::id())->get();
        foreach ($groupProjects as $groupProject) {
            GroupProjectCredential::query()->where('fk_group_project_id', $groupProject->id)->delete();
            TeamMember::query()->where('fk_group_project_id', $groupProject->id)->delete();
            $groupProject->delete();
        }

        $user->delete();

        return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'User account deleted successfully!']], Response::HTTP_OK);
    }
}
