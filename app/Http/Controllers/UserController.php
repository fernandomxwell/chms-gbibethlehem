<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

    public function __construct(private UserService $userService) {}

    public static function middleware(): array
    {
        return [
            new Middleware('navigation', only: [
                'index',
                'create'
            ]),
        ];
    }

    public function index(IndexUserRequest $request)
    {
        try {
            $users = $this->userService->getPaginatedUsers($request);

            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'users.index');
        }
    }

    public function create()
    {
        try {
            return view('users.create');
        } catch (\Exception $e) {
            return $this->handleException($e, 'users.index');
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $this->userService->create($request);

            return redirect()->route('users.index')
                ->with('success', __('users.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'users.index');
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', __('users.cannot_delete_self'));
        }

        try {
            $this->userService->delete($user->id);

            return redirect()->route('users.index')
                ->with('success', __('users.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'users.index');
        }
    }
}
