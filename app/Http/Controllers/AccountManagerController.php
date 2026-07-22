<?php

namespace App\Http\Controllers;

use App\Models\AccountManager;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountManagerController extends Controller
{
    public function index(Request $request): View
    {
        $accountManagers = AccountManager::query()
            ->withCount('clients')
            ->orderBy('account_manager')
            ->paginate(50)
            ->withQueryString();

        $selectedAccountManager = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedAccountManager = AccountManager::query()
                ->withCount('clients')
                ->find($request->integer('view'));
            $panelMode = $selectedAccountManager ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedAccountManager = AccountManager::query()
                ->withCount('clients')
                ->find($request->integer('edit'));
            $panelMode = $selectedAccountManager ? 'edit' : null;
        }

        $analytics = [
            'total' => AccountManager::query()->count(),
            'in_use' => AccountManager::query()->whereHas('clients')->count(),
            'unused' => AccountManager::query()->whereDoesntHave('clients')->count(),
            'related_clients' => Client::query()->whereNotNull('account_manager_id')->count(),
        ];

        return view('setup.account-managers', compact('accountManagers', 'analytics', 'panelMode', 'selectedAccountManager'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'account_manager' => [
                'required',
                'string',
                'max:255',
                Rule::unique('account_manager', 'account_manager'),
            ],
        ]);

        AccountManager::create($data);

        return redirect()
            ->route('account-managers.index')
            ->with('status', 'Account Manager added successfully.');
    }

    public function update(Request $request, AccountManager $accountManager): RedirectResponse
    {
        $data = $request->validate([
            'account_manager' => [
                'required',
                'string',
                'max:255',
                Rule::unique('account_manager', 'account_manager')->ignore($accountManager),
            ],
        ]);

        $accountManager->update($data);

        return redirect()
            ->route('account-managers.index', ['view' => $accountManager->id])
            ->with('status', 'Account Manager updated successfully.');
    }

    public function destroy(AccountManager $accountManager): RedirectResponse
    {
        if ($accountManager->clients()->exists()) {
            return redirect()
                ->route('account-managers.index', ['view' => $accountManager->id])
                ->withErrors(['account_manager' => 'This account manager cannot be deleted because it is related to client records.']);
        }

        $accountManager->delete();

        return redirect()
            ->route('account-managers.index')
            ->with('status', 'Account Manager deleted successfully.');
    }
}
