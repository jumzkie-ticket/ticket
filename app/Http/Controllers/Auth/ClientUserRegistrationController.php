<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClientUserRegistrationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register-client-user');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
            ],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $customerRole = Role::query()
            ->where('slug', 'customer')
            ->where('status', 'active')
            ->firstOrFail();

        $companySearch = trim($data['company_name']);
        $normalizedCompanySearch = mb_strtolower($companySearch);
        $matchingClients = Client::query()
            ->where('status', 'active')
            ->whereRaw('LOWER(company_name) LIKE ?', ["%{$normalizedCompanySearch}%"])
            ->orderBy('company_name')
            ->get(['id', 'company_name']);

        $client = $matchingClients->first(
            fn (Client $candidate): bool => mb_strtolower($candidate->company_name) === $normalizedCompanySearch,
        );

        if (! $client && $matchingClients->count() === 1) {
            $client = $matchingClients->first();
        }

        if (! $client) {
            $message = $matchingClients->isEmpty()
                ? 'No active client company contains the entered company name.'
                : 'Multiple client companies match this name. Enter a more specific company name.';

            throw ValidationException::withMessages([
                'company_name' => $message,
            ]);
        }

        DB::transaction(function () use ($client, $customerRole, $data): void {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            ClientUser::create([
                'user_id' => $user->id,
                'client_id' => $client->id,
            ]);

            $user->roles()->attach($customerRole->id);
        });

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful. You can now sign in.');
    }
}
