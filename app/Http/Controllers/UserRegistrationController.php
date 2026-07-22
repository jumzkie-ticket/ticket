<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserRegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $field = $this->searchField($request->input('field', 'all'));

        $users = User::query()
            ->with(['client', 'roles'])
            ->when($search !== '', fn ($query) => $this->applySearch($query, $field, $search))
            ->orderByDesc('created_at')
            ->orderBy('first_name')
            ->paginate(8)
            ->withQueryString();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        $clients = Client::query()
            ->orderBy('company_name')
            ->get(['id', 'company_name']);

        $modalMode = null;
        $selectedUser = null;

        if ($request->filled('view')) {
            $selectedUser = User::with(['client', 'roles'])->find($request->integer('view'));
            $modalMode = $selectedUser ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedUser = User::with(['client', 'roles'])->find($request->integer('edit'));
            $modalMode = $selectedUser ? 'edit' : null;
        } elseif ($request->boolean('create')) {
            $modalMode = 'create';
        }

        $stats = [
            'total_users' => User::count(),
            'admins' => $this->usersInRole('admin'),
            'customers' => $this->usersInRole('customer'),
            'consultants' => $this->usersInRole('consultant'),
        ];

        return view('users.index', compact('clients', 'field', 'modalMode', 'roles', 'search', 'selectedUser', 'stats', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedUser($request);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            ClientUser::create([
                'user_id' => $user->id,
                'client_id' => $data['clients_id'],
            ]);

            $user->roles()->sync([$data['role_id']]);
        });

        return redirect()
            ->route('users.index')
            ->with('status', 'User registered successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedUser($request, $user);

        $userData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        DB::transaction(function () use ($data, $user, $userData): void {
            $user->update($userData);
            $user->clientUser()->updateOrCreate([], [
                'client_id' => $data['clients_id'],
            ]);
            $user->roles()->sync([$data['role_id']]);
        });

        return redirect()
            ->route('users.index', ['view' => $user->id])
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'User deleted successfully.');
    }

    /**
     * @return array{clients_id:int,first_name:string,last_name:string,email:string,password?:string,role_id:int}
     */
    private function validatedUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'clients_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [
                $user ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);
    }

    private function applySearch($query, string $field, string $search): void
    {
        match ($field) {
            'first_name', 'last_name', 'email' => $query->where($field, 'like', "%{$search}%"),
            'role' => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'like', "%{$search}%")),
            default => $query->where(function ($innerQuery) use ($search) {
                $innerQuery
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'like', "%{$search}%"));
            }),
        };
    }

    private function searchField(mixed $field): string
    {
        $field = is_string($field) ? $field : 'all';

        return in_array($field, ['all', 'first_name', 'last_name', 'email', 'role'], true)
            ? $field
            : 'all';
    }

    private function usersInRole(string $slug): int
    {
        return User::whereHas('roles', fn ($query) => $query->where('slug', $slug))->count();
    }
}
