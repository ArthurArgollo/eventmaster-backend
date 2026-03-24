<?php

namespace App\Http\Controllers;

use App\Models\OrganizerRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrganizerRequestController extends Controller
{
    /**
     * Submit a new organizer request (public).
     * Organizers are natural persons: name, CPF, email, optional phone and reason.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'max:14', 'unique:organizer_requests,cpf', 'unique:users,cpf'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:organizer_requests,email', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:32'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => __('The given data was invalid.'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $organizerRequest = OrganizerRequest::create($validator->validated());

        return response()->json([
            'message' => __('Organizer request submitted successfully.'),
            'organizer_request' => $organizerRequest,
        ], 201);
    }

    /**
     * List all organizer requests (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrganizerRequest::query()->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->paginate($request->integer('per_page', 15));

        return response()->json($requests);
    }

    /**
     * Show a single organizer request (admin).
     */
    public function show(OrganizerRequest $organizerRequest): JsonResponse
    {
        return response()->json($organizerRequest);
    }

    /**
     * Approve an organizer request (admin).
     * Creates a new user with the organizer role using the request's person data and a random password.
     */
    public function approve(OrganizerRequest $organizerRequest): JsonResponse
    {
        if ($organizerRequest->status !== 'pending') {
            return response()->json([
                'message' => $this->organizerRequestNotPendingMessage($organizerRequest),
            ], 422);
        }

        if (blank($organizerRequest->cpf) || blank($organizerRequest->email)) {
            return response()->json([
                'message' => __('This request is missing CPF or email and cannot be approved.'),
            ], 422);
        }

        if (User::query()
            ->where(function ($query) use ($organizerRequest) {
                $query->where('email', $organizerRequest->email)
                    ->orWhere('cpf', $organizerRequest->cpf);
            })
            ->exists()) {
            return response()->json([
                'message' => __('A user with this email or CPF already exists. Resolve the conflict before approving.'),
            ], 409);
        }

        $role = Role::where('name', 'organizer')->first();

        if (! $role) {
            return response()->json([
                'message' => __('Organizer role not configured.'),
            ], 500);
        }

        $temporaryPassword = Str::random(16);

        $user = User::create([
            'id_role' => $role->id,
            'name' => $organizerRequest->name,
            'cpf' => $organizerRequest->cpf,
            'email' => $organizerRequest->email,
            'password' => Hash::make($temporaryPassword),
        ]);

        $organizerRequest->update(['status' => 'approved']);

        $user->load('role');

        return response()->json([
            'message' => __('Organizer request approved successfully.'),
            'user' => [
                'id' => $user->id,
                'id_role' => $user->id_role,
                'role' => $user->role->name,
                'name' => $user->name,
                'cpf' => $user->cpf,
                'email' => $user->email,
            ],
            'temporary_password' => $temporaryPassword,
        ]);
    }

    /**
     * Reject an organizer request (admin).
     */
    public function reject(OrganizerRequest $organizerRequest): JsonResponse
    {
        if ($organizerRequest->status !== 'pending') {
            return response()->json([
                'message' => $this->organizerRequestNotPendingMessage($organizerRequest),
            ], 422);
        }

        $organizerRequest->update(['status' => 'rejected']);

        return response()->json([
            'message' => __('Organizer request rejected successfully.'),
        ]);
    }

    private function organizerRequestNotPendingMessage(OrganizerRequest $organizerRequest): string
    {
        return match ($organizerRequest->status) {
            'approved' => __('This organizer request has already been approved.'),
            'rejected' => __('This organizer request has already been rejected.'),
            default => __('This organizer request is no longer pending.'),
        };
    }
}
