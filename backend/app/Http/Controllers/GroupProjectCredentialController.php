<?php

namespace App\Http\Controllers;

use App\Models\GroupActionHistory;
use App\Models\GroupProject;
use App\Models\GroupProjectCredential;
use App\Models\TeamMember;
use App\Models\User;
use App\Notifications\GroupCredentialHistory;
use App\Services\EthereumContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class GroupProjectCredentialController extends Controller
{
    protected $ethereumContractService;

    public function __construct(EthereumContractService $ethereumContractService)
    {
        $this->ethereumContractService = $ethereumContractService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index($groupProjectId): JsonResponse
    {
        try {
            $isPermitted = $this->validatePermission($groupProjectId);
            if (!$isPermitted) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $result = $this->ethereumContractService->getGroupUserCredentials($groupProjectId);
            return response()->json(['success' => true, 'data' => $result, 'message' => []], Response::HTTP_OK);

            $projectCredentials = GroupProjectCredential::with('groupProject')->where('fk_group_project_id', $groupProjectId)->get();

            return response()->json(['success' => true, 'data' => $projectCredentials->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $groupProjectId): JsonResponse
    {
        try {
            $isPermitted = $this->validatePermission($groupProjectId);
            if (!$isPermitted) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $validator = Validator::make($request->all(), [
                'site_name' => 'required|string|max:255',
                'site_url' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group Project Not found!']], Response::HTTP_NOT_FOUND);
            }

            $projectCredential = GroupProjectCredential::query()->create([
                'site_name' => $validated['site_name'],
                'site_url' => $validated['site_url'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'fk_group_project_id' => $groupProject->id
            ]);

            // If not group owner
            if($groupProject->fk_user_id != Auth::id()) {
                $editor = TeamMember::query()->where('fk_group_project_id', $groupProject->id)->where('fk_role_id', 2)->where('fk_user_id', Auth::id())->first();
                if ($editor) {
                    GroupActionHistory::query()->create([
                        'fk_team_member_id' => $editor->id,
                        'fk_group_project_cred_id' => $projectCredential->id,
                        'actions_taken' => 'New Group Project Credential Created!',
                    ]);

                    $adminUserIds = TeamMember::query()->where('fk_group_project_id', $groupProjectId)->where('fk_role_id', 1)->pluck('fk_user_id');
                    $groupOwnerAndAdminUsers = User::query()->where('id', $groupProject->fk_user_id)->orWhereIn('id', $adminUserIds)->get();
                    if ($groupOwnerAndAdminUsers->count() > 0) {
                        $message = Auth::user()->name . " has created a new credential named '" . $projectCredential->site_name . "' in '" . $groupProject->name . "' group project!";
                        Notification::send($groupOwnerAndAdminUsers, new GroupCredentialHistory($message));
                    }
                }
            }

            $result = $this->ethereumContractService->saveGroupCredentialByApi($projectCredential->id, $groupProject->id, $validated['username'], $validated['password'], $validated['site_url'], $validated['site_name']);
            return response()->json(['success' => true, 'data' => $projectCredential->toArray(), 'message' => ['statusText' => 'Project credential created successfully!']], Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $groupProjectId, string $id): JsonResponse
    {
        try {
            $isPermitted = $this->validatePermission($groupProjectId);
            if (!$isPermitted) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }
            
            $projectCredential = GroupProjectCredential::with('groupProject')->where('id', $id)->where('fk_group_project_id', $groupProjectId)->first();
            if (!$projectCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Project credential not found!']], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['success' => true, 'data' => $projectCredential->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $groupProjectId, string $id): JsonResponse
    {
        try {
            $isPermitted = $this->validatePermission($groupProjectId);
            if (!$isPermitted) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $validator = Validator::make($request->all(), [
                'site_name' => 'required|string|max:255',
                'site_url' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group Project Not found!']], Response::HTTP_NOT_FOUND);
            }

            $projectCredential = GroupProjectCredential::query()->where('id', $id)->where('fk_group_project_id', $groupProject->id)->first();
            if (!$projectCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Project credential not found!']], Response::HTTP_NOT_FOUND);
            }

            $projectCredential->update([
                'site_name' => $validated['site_name'],
                'site_url' => $validated['site_url'],
                'username' => $validated['username'],
                'password' => $validated['password'],
            ]);

            // If not group owner
            if($groupProject->fk_user_id != Auth::id()) {
                $editor = TeamMember::query()->where('fk_group_project_id', $groupProject->id)->where('fk_role_id', 2)->where('fk_user_id', Auth::id())->first();
                if ($editor) {
                    GroupActionHistory::query()->create([
                        'fk_team_member_id' => $editor->id,
                        'fk_group_project_cred_id' => $projectCredential->id,
                        'actions_taken' => 'Group Project Credential Updated!',
                    ]);

                    $adminUserIds = TeamMember::query()->where('fk_group_project_id', $groupProject->id)->where('fk_role_id', 1)->pluck('fk_user_id');
                    $groupOwnerAndAdminUsers = User::query()->where('id', $groupProject->fk_user_id)->orWhereIn('id', $adminUserIds)->get();
                    if ($groupOwnerAndAdminUsers->count() > 0) {
                        $message = Auth::user()->name . " has updated a credential named '" . $projectCredential->site_name . "' in '" . $groupProject->name . "' group project!";
                        Notification::send($groupOwnerAndAdminUsers, new GroupCredentialHistory($message));
                    }
                }
            }

            $result = $this->ethereumContractService->updateGroupCredentialByApi($projectCredential->id, $groupProject->id, $validated['username'], $validated['password'], $validated['site_url'], $validated['site_name']);
            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Project credential updated successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $groupProjectId, string $id): JsonResponse
    {
        try {
            $isPermitted = $this->validatePermission($groupProjectId);
            if (!$isPermitted) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Permission Denied!']], Response::HTTP_NOT_ACCEPTABLE);
            }

            $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
            if (!$groupProject) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Group Project Not found!']], Response::HTTP_NOT_FOUND);
            }

            $projectCredential = GroupProjectCredential::query()->where('id', $id)->where('fk_group_project_id', $groupProject->id)->first();
            if (!$projectCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Project credential not found!']], Response::HTTP_NOT_FOUND);
            }

            // If not group owner
            if($groupProject->fk_user_id != Auth::id()) {
                $editor = TeamMember::query()->where('fk_group_project_id', $groupProject->id)->where('fk_role_id', 2)->where('fk_user_id', Auth::id())->first();
                if ($editor) {
                    GroupActionHistory::query()->create([
                        'fk_team_member_id' => $editor->id,
                        'fk_group_project_cred_id' => $projectCredential->id,
                        'actions_taken' => 'Group Project Credential Deleted!',
                    ]);

                    $adminUserIds = TeamMember::query()->where('fk_group_project_id', $groupProject->id)->where('fk_role_id', 1)->pluck('fk_user_id');
                    $groupOwnerAndAdminUsers = User::query()->where('id', $groupProject->fk_user_id)->orWhereIn('id', $adminUserIds)->get();
                    if ($groupOwnerAndAdminUsers->count() > 0) {
                        $message = Auth::user()->name . " has deleted a credential named '" . $projectCredential->site_name . "' in '" . $groupProject->name . "' group project!";
                        Notification::send($groupOwnerAndAdminUsers, new GroupCredentialHistory($message));
                    }
                }
            }

            $projectCredential->delete();
            $result = $this->ethereumContractService->deleteGroupCredentialByApi($projectCredential->id, $groupProject->id);

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Project credential deleted successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function validatePermission($groupProjectId): bool
    {
        $groupProject = GroupProject::query()->where('id', $groupProjectId)->first();
        if (!$groupProject) {
            return false;
        }

        $teamMemberUserIds = TeamMember::query()->where('fk_group_project_id', $groupProjectId)->where('fk_user_id', '!=', null)->pluck('fk_user_id')->toArray();

        return !($groupProject->fk_user_id !== Auth::id() && !in_array(Auth::id(), $teamMemberUserIds, false));
    }
}
