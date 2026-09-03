<?php

namespace App\Http\Controllers;

use App\Models\GroupActionHistory;
use App\Models\GroupProject;
use App\Models\GroupProjectCredential;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\GroupCredentialHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GroupProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {

            $otherGroupProjects = TeamMember::query()->where('fk_user_id', Auth::id())->where('status', 'accepted')->pluck('fk_group_project_id')->toArray();
            $groupProjects = GroupProject::with('groupOwner')
                ->where(function ($query) use ($otherGroupProjects) {
                    $query->where('fk_user_id', Auth::id())->orWhereIn('id', $otherGroupProjects);
                })->get();

            $groupProjectsArray = $groupProjects->toArray();

            foreach ($groupProjectsArray as $key => $groupProject) {
                if ($groupProject['fk_user_id'] === Auth::id()) {
                    $groupProjectsArray[$key]['user_role'] = 'Owner';
                } else {
                    $groupProjectsArray[$key]['user_role'] = TeamMember::with('role')->where('fk_group_project_id', $groupProject['id'])->where('fk_user_id', Auth::id())->where('status', 'accepted')->first()?->role->name;
                }
            }

            return response()->json(['success' => true, 'data' => $groupProjectsArray, 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $groupProject = GroupProject::query()->create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'fk_user_id' => Auth::id()
            ]);

            return response()->json(['success' => true, 'data' => $groupProject->toArray(), 'message' => ['statusText' => 'Group project created successfully!']], Response::HTTP_CREATED);
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
            $groupProject = GroupProject::with('groupOwner')->where('id', $id)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            $groupProjectArray = $groupProject->toArray();
            if ($groupProjectArray['fk_user_id'] === Auth::id()) {
                $groupProjectArray['user_role'] = 'Owner';
            } else {
                $groupProjectArray['user_role'] = TeamMember::with('role')->where('fk_group_project_id', $groupProjectArray['id'])->where('fk_user_id', Auth::id())->where('status', 'accepted')->first()?->role->name;
            }

            return response()->json(['success' => true, 'data' => $groupProjectArray, 'message' => []], Response::HTTP_OK);
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
                'name' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $groupProject = GroupProject::query()->where('id', $id)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }
            if ($groupProject->fk_user_id != Auth::id()) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Unauthorized!']], Response::HTTP_UNAUTHORIZED);
            }

            $groupProject->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Group project updated successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $groupProject = GroupProject::query()->where('id', $id)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            GroupProjectCredential::query()->where('fk_group_project_id', $groupProject->id)->delete();
            TeamMember::query()->where('fk_group_project_id', $groupProject->id)->delete();
            $groupProject->delete();

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Group project deleted successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendInvitation(Request $request, $group_project_id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'description' => 'required|string',
                'fk_role_id' => 'required|numeric',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $adminEditorUserIds = TeamMember::query()
                ->where('fk_group_project_id', $group_project_id)
                ->where('fk_role_id', '!=', 3)
                ->where('status', 'accepted')
                ->pluck('fk_user_id')->toArray();

            $groupProject = GroupProject::query()->where('id', $group_project_id)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            if ($groupProject->fk_user_id != Auth::id() && !in_array(Auth::id(), $adminEditorUserIds)) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_FORBIDDEN);
            }

            $invitationToken = Str::random(60);

            //Send Mail
            Mail::send('emails.groupInvitationEmail', [
                'groupProject' => $groupProject,
                'token' => $invitationToken,
                'senderName' => Auth::user()->name
            ], function ($message) use ($validated, $groupProject) {
                $message->to($validated['email'])->subject('Invitation to ' . $groupProject->name);
            });

            $teamMember = TeamMember::query()->where('fk_group_project_id', $group_project_id)->where('email', $validated['email'])->first();
            if ($teamMember) {
                $teamMember->update([
                    'description' => $validated['description'],
                    'fk_role_id' => $validated['fk_role_id'],
                    'invitation_token' => $invitationToken,
                    'status' => 'pending',
                ]);
            } else {
                $user = User::query()->where('email', $validated['email'])->first();

                TeamMember::query()->create([
                    'fk_group_project_id' => $group_project_id,
                    'description' => $validated['description'],
                    'fk_role_id' => $validated['fk_role_id'],
                    'email' => $validated['email'],
                    'invitation_token' => $invitationToken,
                    'fk_user_id' => $user->id ?? null,
                    'status' => 'pending',
                ]);
            }

            if($groupProject->fk_user_id != Auth::id()) {
                $teamMember = TeamMember::query()->where('fk_group_project_id', $group_project_id)->where('fk_role_id', 2)->where('fk_user_id', Auth::id())->first();
                if ($teamMember) {
                    GroupActionHistory::query()->create([
                        'fk_team_member_id' => $teamMember->id,
                        'actions_taken' => 'New Invitation send to ' . $validated['email'],
                    ]);

                    $adminUserIds = TeamMember::query()->where('fk_group_project_id', $group_project_id)->where('fk_role_id', 1)->pluck('fk_user_id');
                    $adminUsers = User::query()->whereIn('id', $adminUserIds)->get();
                    if ($adminUsers->count() > 0) {
                        $groupProjectName = GroupProject::query()->where('id', $group_project_id)->first()?->name;
                        $message = Auth::user()->name . " has invited '" . $validated['email'] . "' to join the '" . $groupProjectName . "' group project.";
                        Notification::send($adminUsers, new GroupCredentialHistory($message));
                    }
                }
            }

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Group project invitation sent successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyInvitation($userId, $token): JsonResponse
    {
        try {
            $user = User::query()->where('id', $userId)->first();
            if (!$user) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'User not found!']], Response::HTTP_NOT_FOUND);
            }

            $teamMember = TeamMember::query()->where('email', $user->email)->where('invitation_token', $token)->first();
            if (!$teamMember) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Invalid Token!']], Response::HTTP_NOT_FOUND);
            }

            $teamMember->update([
                'fk_user_id' => $user->id,
                'status' => 'accepted',
            ]);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Group project invitation accepted successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function memberList($groupProjectId): JsonResponse
    {
        try {
            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            $teamMemberUserIds = TeamMember::query()->where('fk_group_project_id', $groupProjectId)->where('fk_user_id', '!=', null)->pluck('fk_user_id')->toArray();

            if ($groupProject->fk_user_id !== Auth::id() && !in_array(Auth::id(), $teamMemberUserIds, false)) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $groupTeamMembers = TeamMember::with('groupProject', 'user', 'role')->where('fk_group_project_id', $groupProjectId)->where('fk_user_id', '!=', null)->get();

            return response()->json(['success' => true, 'data' => $groupTeamMembers->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function leaveProject($groupProjectId): JsonResponse
    {
        try {
            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            $teamMember = TeamMember::query()->where('fk_group_project_id', $groupProjectId)->where('fk_user_id', Auth::id())->where('status', 'accepted')->first();
            if (!$teamMember) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $teamMember->delete();

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Group project left successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(string $query): JsonResponse
    {
        try {
            $otherGroupProjects = TeamMember::query()->where('fk_user_id', Auth::id())->where('status', 'accepted')->pluck('fk_group_project_id')->toArray();
            $groupProjects = GroupProject::with('groupOwner')
                ->where(function ($query) use ($otherGroupProjects) {
                    $query->where('fk_user_id', Auth::id())->orWhereIn('id', $otherGroupProjects);
                })
                ->where('name', 'LIKE', '%' . $query . '%')
                ->get();

            $groupProjectsArray = $groupProjects->toArray();

            foreach ($groupProjectsArray as $key => $groupProject) {
                if ($groupProject['fk_user_id'] === Auth::id()) {
                    $groupProjectsArray[$key]['user_role'] = 'Owner';
                } else {
                    $groupProjectsArray[$key]['user_role'] = TeamMember::with('role')->where('fk_group_project_id', $groupProject['id'])->where('fk_user_id', Auth::id())->where('status', 'accepted')->first()?->role->name;
                }
            }

            return response()->json(['success' => true, 'data' => $groupProjectsArray, 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function actionHistories($groupProjectId): JsonResponse
    {
        try {
            $adminUserIds = TeamMember::query()
                ->where('fk_group_project_id', $groupProjectId)
                ->where('fk_role_id', 1)
                ->where('status', 'accepted')
                ->pluck('fk_user_id')
                ->toArray();

            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group project not found!']], Response::HTTP_NOT_FOUND);
            }

            if ($groupProject->fk_user_id != Auth::id() && !in_array(Auth::id(), $adminUserIds)) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_FORBIDDEN);
            }

            $groupActionHistories = GroupActionHistory::with('teamMember.user', 'teamMember.role', 'groupProjectCredential.groupProject')
                ->whereHas('groupProjectCredential', function ($query) use ($groupProjectId) {
                    $query->where('fk_group_project_id', $groupProjectId);
                })
                ->get();

            return response()->json(['success' => true, 'data' => $groupActionHistories->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
