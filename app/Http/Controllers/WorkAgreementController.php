<?php

namespace App\Http\Controllers;

use App\Models\AssignFc;
use App\Models\Client;
use App\Models\WorkAgreement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkAgreementController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::query()->orderBy('company_name')->get();
        $assignFcs = AssignFc::query()->orderBy('assign_fc')->get();
        $agreements = WorkAgreement::query()
            ->with([
                'client:id,company_name',
                'assignFc:id,assign_fc,designation',
                'projectManager:id,assign_fc,designation',
                'consultantFc:id,assign_fc,designation',
            ])
            ->latest('agreement_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
        $editingAgreement = $request->filled('edit')
            ? WorkAgreement::query()->findOrFail($request->integer('edit'))
            : null;

        return view('customer-management.work-agreements', [
            'agreements' => $agreements,
            'assignFcs' => $assignFcs,
            'assignFcData' => $assignFcs->mapWithKeys(fn (AssignFc $assignFc): array => [
                (string) $assignFc->id => ['designation' => $assignFc->designation ?? ''],
            ]),
            'clientData' => $clients->mapWithKeys(fn (Client $client): array => [
                (string) $client->id => ['address' => $this->completeAddress($client)],
            ]),
            'clients' => $clients,
            'editingAgreement' => $editingAgreement,
            'nextAgreementNumber' => 'WA-'.str_pad((string) (WorkAgreement::query()->max('id') + 1), 4, '0', STR_PAD_LEFT),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['created_by'] = $request->user()->id;

        WorkAgreement::create($data);

        return redirect()->route('work-agreements.index')->with('status', 'Work agreement created successfully.');
    }

    public function update(Request $request, WorkAgreement $workAgreement): RedirectResponse
    {
        $workAgreement->update($this->validatedData($request));

        return redirect()
            ->route('work-agreements.index', ['edit' => $workAgreement->id])
            ->with('status', 'Work agreement updated successfully.');
    }

    public function destroy(WorkAgreement $workAgreement): RedirectResponse
    {
        $workAgreement->delete();

        return redirect()->route('work-agreements.index')->with('status', 'Work agreement deleted successfully.');
    }

    /** @return array<string, mixed> */
    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'agreement_date' => ['required', 'date'],
            'assign_fc_id' => ['required', 'integer', Rule::exists('assign_fc', 'id')],
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')],
            'billable' => ['required', 'boolean'],
            'non_billable' => ['required', 'boolean'],
            'scope' => ['required', 'string', 'max:10000'],
            'objective' => ['required', 'string', 'max:10000'],
            'current_issue' => ['required', 'string', 'max:10000'],
            'proposed_solutions' => ['required', 'string', 'max:10000'],
            'note' => ['nullable', 'string', 'max:5000'],
            'estimated_man_days' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'project_manager_assign_fc_id' => ['required', 'integer', Rule::exists('assign_fc', 'id')],
            'consultant_assign_fc_id' => ['required', 'integer', Rule::exists('assign_fc', 'id')],
            'accepted_by' => ['required', 'string', 'max:255'],
            'accepted_by_designation' => ['required', 'string', 'max:255'],
        ]);

        if ((bool) $data['billable'] === (bool) $data['non_billable']) {
            throw ValidationException::withMessages([
                'billable' => 'Select either Billable or Non-Billable.',
            ]);
        }

        $client = Client::query()->findOrFail($data['client_id']);
        $data['address'] = $this->completeAddress($client);
        $projectManager = AssignFc::query()->findOrFail($data['project_manager_assign_fc_id']);
        $consultant = AssignFc::query()->findOrFail($data['consultant_assign_fc_id']);
        $data['project_manager'] = $projectManager->assign_fc;
        $data['consultant'] = $consultant->assign_fc;

        return $data;
    }

    private function completeAddress(Client $client): string
    {
        return collect([
            $client->building_details,
            $client->barangay_name,
            $client->city_municipality_name,
            $client->province_name,
            $client->region_name,
        ])->filter(fn (?string $part): bool => filled($part))->implode(', ');
    }
}
