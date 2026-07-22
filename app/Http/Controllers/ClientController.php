<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AccountManager;
use App\Models\IndustryBusinessType;
use App\Models\SapProduct;
use App\Models\AssignFc;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
        ];
    }

    public function index(Request $request): View
    {
        $clients = Client::query()
            ->with([
                'accountManager:id,account_manager',
                'industryBusinessType:id,industry',
                'sapProducts:id,sap_product',
            ])
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $selectedClient = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedClient = Client::query()
                ->with(['accountManager', 'industryBusinessType', 'sapProducts', 'registeredBy'])
                ->find($request->integer('view'));
            $panelMode = $selectedClient ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedClient = Client::query()
                ->with(['accountManager', 'industryBusinessType', 'sapProducts', 'registeredBy'])
                ->find($request->integer('edit'));
            $panelMode = $selectedClient ? 'edit' : null;
        }

        $analytics = $this->analytics();

        return view('clients.index', [
            'analytics' => $analytics,
            'accountManagers' => AccountManager::query()->orderBy('account_manager')->get(),
            'clients' => $clients,
            'companySizes' => ClientRegistrationController::companySizes(),
            'industries' => IndustryBusinessType::query()->orderBy('industry')->get(),
            'panelMode' => $panelMode,
            'sapProducts' => SapProduct::query()->orderBy('sap_product')->get(),
            'selectedClient' => $selectedClient,
            'statuses' => self::statuses(),
            'supportMethods' => ClientRegistrationController::supportMethods(),
            'assignFcs' => AssignFc::query()->orderBy('assign_fc')->get(),
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $this->validatedClient($request);
        $productIds = array_values(array_unique(array_map('intval', $data['sap_product_ids'])));
        unset($data['sap_product_ids']);
        $client->update($data);
        $client->sapProducts()->sync($productIds);

        return redirect()
            ->route('clients.index', ['view' => $client->id])
            ->with('status', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('status', 'Client deleted successfully.');
    }

    /** @return array<string, int> */
    private function analytics(): array
    {
        $monthStart = CarbonImmutable::now()->startOfMonth();

        return [
            'total' => Client::query()->count(),
            'active' => Client::query()->where('status', 'active')->count(),
            'new_this_month' => Client::query()->where('created_at', '>=', $monthStart)->count(),
            'industries' => Client::query()->whereNotNull('industry_business_type_id')->distinct()->count('industry_business_type_id'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedClient(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email_address' => ['required', 'email', 'max:255'],
            'contact_country_code' => ['required', 'string', Rule::in(['+63'])],
            'contact_number' => ['required', 'string', 'max:12', 'regex:/^\d{3}-\d{3}-\d{4}$/'],
            'industry_business_type_id' => ['required', 'integer', Rule::exists('industry_business_types', 'id')],
            'sap_product_ids' => ['required', 'array', 'min:1'],
            'sap_product_ids.*' => ['integer', 'distinct', Rule::exists('products', 'id')],
            'version_number' => ['required', 'string', 'max:40'],
            'patch_or_fp' => ['required', 'string', 'max:120'],
            'db_version' => ['required', 'string', 'max:120'],
            'company_size' => ['required', Rule::in(array_keys(ClientRegistrationController::companySizes()))],
            'account_manager_id' => ['nullable', 'integer', Rule::exists('account_manager', 'id')],
            'assign_fc_id' => ['nullable', 'integer', Rule::exists('assign_fc', 'id')],
            'preferred_support_method' => ['required', Rule::in(array_keys(ClientRegistrationController::supportMethods()))],
            'status' => ['required', Rule::in(array_keys(self::statuses()))],
        ], [
            'contact_number.regex' => 'The contact number must use the format 000-000-0000.',
        ]);
    }
}
