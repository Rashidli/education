<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        return view('users.index', [
            'users' => $query->latest()->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'user' => new User(),
            'roles' => Role::latest()->get(),
            'userRoles' => [],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->syncRoles($request->input('roles', []));

        return redirect()->route('users.index')
            ->with('success', 'İstifadəçi əlavə olundu.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => Role::latest()->get(),
            'userRoles' => $user->roles->pluck('name')->toArray(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ];

        if ($password = $request->input('password')) {
            $data['password'] = Hash::make($password);
        }

        $user->update($data);

        if ($user->id !== $request->user()->id) {
            $newRoles = $request->input('roles', []);

            if ($user->hasRole(User::ROLE_SUPER_ADMIN)
                && ! in_array(User::ROLE_SUPER_ADMIN, $newRoles)
                && User::role(User::ROLE_SUPER_ADMIN)->count() <= 1) {
                return back()->with('error', 'Sistemdə ən azı bir Super Admin qalmalıdır — bu istifadəçinin rolu dəyişdirilə bilməz.');
            }

            $user->syncRoles($newRoles);
        }

        return redirect()->route('users.index')
            ->with('success', 'İstifadəçi yeniləndi.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Özünüzü silə bilməzsiniz.');
        }

        if ($user->hasRole(User::ROLE_SUPER_ADMIN) && User::role(User::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()->with('error', 'Sistemdə ən azı bir Super Admin qalmalıdır.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'İstifadəçi silindi.');
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }
}
