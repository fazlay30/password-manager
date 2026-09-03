<?php

namespace App\Http\Controllers;

use App\Models\UserCredential;
use App\Services\EthereumContractService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserCredentialController extends Controller
{
    protected $ethereumContractService;

    public function __construct(EthereumContractService $ethereumContractService)
    {
        $this->ethereumContractService = $ethereumContractService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $result = $this->ethereumContractService->getUserCredentials(Auth::id());
            return response()->json(['success' => true, 'data' => $result, 'message' => []], Response::HTTP_OK);
            
            $userCredentials = UserCredential::with('user')->where('fk_user_id', Auth::id())->get();
            return response()->json(['success' => true, 'data' => $userCredentials->toArray(), 'message' => []], Response::HTTP_OK);
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
                'site_name' => 'required|string|max:255',
                'site_url' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $userCredential = UserCredential::query()->create([
                'site_name' => $validated['site_name'],
                'site_url' => $validated['site_url'],
                'username' => $validated['username'],
                'password' => $validated['password'],
                'fk_user_id' => Auth::id()
            ]);
            
            
            $result = $this->ethereumContractService->saveUserCredentialByApi($userCredential->id, Auth::id(), $validated['username'], $validated['password'], $validated['site_url'], $validated['site_name']);
            
            return response()->json(['success' => true, 'data' => $userCredential->toArray(), 'message' => ['statusText' => 'Credential created successfully!']], Response::HTTP_CREATED);
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
            $userCredential = UserCredential::with('user')->where('id', $id)->first();
            if (!$userCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Credential not found!']], Response::HTTP_NOT_FOUND);
            }
            if ($userCredential->fk_user_id != Auth::id()) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Unauthorized!']], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json(['success' => true, 'data' => $userCredential->toArray(), 'message' => []], Response::HTTP_OK);
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
                'site_name' => 'required|string|max:255',
                'site_url' => 'required|string|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
            ]);

            if ( $validator->fails() ) {
                return response()->json(['success' => false, 'data' => [], 'message' => $validator->errors()->toArray()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validate();

            $userCredential = UserCredential::query()->where('id', $id)->first();
            if (!$userCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Credential not found!']], Response::HTTP_NOT_FOUND);
            }
            if ($userCredential->fk_user_id != Auth::id()) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Unauthorized!']], Response::HTTP_UNAUTHORIZED);
            }

            $userCredential->update([
                'site_name' => $validated['site_name'],
                'site_url' => $validated['site_url'],
                'username' => $validated['username'],
                'password' => $validated['password'],
            ]);

            $result = $this->ethereumContractService->updateUserCredentialByApi($userCredential->id, Auth::id(), $validated['username'], $validated['password'], $validated['site_url'], $validated['site_name']);
            
            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Credential updated successfully!']], Response::HTTP_OK);
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
            $userCredential = UserCredential::query()->where('id', $id)->first();
            if (!$userCredential) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Credential not found!']], Response::HTTP_NOT_FOUND);
            }
            if ($userCredential->fk_user_id != Auth::id()) {
                return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => 'Unauthorized!']], Response::HTTP_UNAUTHORIZED);
            }

            $userCredential->delete();
            $result = $this->ethereumContractService->deleteUserCredentialByApi($userCredential->id, Auth::id());

            return response()->json(['success' => true, 'data' => [], 'message' => ['statusText' => 'Credential deleted successfully!']], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(string $query): JsonResponse
    {
        try {
            $userCredentials = UserCredential::with('user')
                ->where('fk_user_id', Auth::id())
                ->where(function ($q) use ($query) {
                    $q->where('site_name', 'LIKE', '%' . $query . '%')
                        ->orWhere('site_url', 'LIKE', '%' . $query . '%');
                })
                ->get();
            return response()->json(['success' => true, 'data' => $userCredentials->toArray(), 'message' => []], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'data' => [], 'message' => ['statusText' => $exception->getMessage()]], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
