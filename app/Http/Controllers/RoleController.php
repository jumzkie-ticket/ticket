<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('permissions', function ($permissionQuery) use ($search) {
                        $permissionQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        $permissions = Permission::query()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $editingRole = $request->filled('edit')
            ? Role::with('permissions')->find($request->integer('edit'))
            : null;

        $viewRole = $request->filled('view')
            ? Role::with(['permissions', 'users'])->withCount('users')->find($request->integer('view'))
            : null;

        $analyticsRoleCount = Role::whereHas('permissions', function ($query) {
            $query->where('slug', 'analytics');
        })->count();

        $stats = [
            'total_roles' => Role::count(),
            'analytics_roles' => $analyticsRoleCount,
            'assigned_users' => User::whereHas('roles')->count(),
            'permission_groups' => Permission::distinct('category')->count('category'),
        ];

        return view('roles.index', compact('editingRole', 'permissions', 'roles', 'search', 'stats', 'viewRole'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedRole($request);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role saved successfully.');
    }

    public function show(Role $role): View
    {
        $role->load(['permissions', 'users'])->loadCount('users');

        return view('roles.show', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validatedRole($request, $role);

        $role->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $role),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('status', 'Role deleted successfully.');
    }

    /**
     * @return array{name:string,description?:string,status?:string,permissions?:array<int, int>}
     */
    private function validatedRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('roles', 'name')->ignore($role),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }

    private function uniqueSlug(string $name, ?Role $role = null): string
    {
        $baseSlug = Str::slug($name) ?: 'role';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Role::where('slug', $slug)
                ->when($role, fn ($query) => $query->whereKeyNot($role->getKey()))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
