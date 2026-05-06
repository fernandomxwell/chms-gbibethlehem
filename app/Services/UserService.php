<?php

namespace App\Services;

use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Notifications\NewUserCredentials;
use Illuminate\Support\Str;

class UserService
{
    public function getPaginatedUsers(IndexUserRequest $request)
    {
        $validatedData = $request->validated();

        return User::searchBy($validatedData)
            ->select([
                'id',
                'name',
                'email',
                'created_at',
            ])
            ->latest()
            ->paginate()
            ->withQueryString();
    }

    public function create(StoreUserRequest $request): User
    {
        $data = $request->validated();
        $plainPassword = Str::password(12);
        $data['password'] = $plainPassword;

        $user = User::create($data);
        $user->notify(new NewUserCredentials($plainPassword));

        return $user;
    }

    public function delete(int $id): void
    {
        User::findOrFail($id, ['id'])->delete();
    }
}
