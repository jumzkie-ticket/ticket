<?php

namespace App\Http\Controllers;

use App\Models\AssignFc;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssignFcController extends Controller
{
    public function index(Request $request): View
    {
        $assignFcs = AssignFc::query()
            ->withCount('clients')
            ->orderBy('assign_fc')
            ->paginate(50)
            ->withQueryString();

        $selected = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selected = AssignFc::query()->withCount('clients')->find($request->integer('view'));
            $panelMode = $selected ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selected = AssignFc::query()->withCount('clients')->find($request->integer('edit'));
            $panelMode = $selected ? 'edit' : null;
        }

        $analytics = [
            'total' => AssignFc::query()->count(),
            'in_use' => AssignFc::query()->whereHas('clients')->count(),
            'unused' => AssignFc::query()->whereDoesntHave('clients')->count(),
            'related_clients' => Client::query()->whereNotNull('assign_fc_id')->count(),
        ];

        return view('setup.assign-fc', compact('assignFcs', 'analytics', 'panelMode', 'selected'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'assign_fc' => ['required', 'string', 'max:255', Rule::unique('assign_fc', 'assign_fc')],
            'designation' => ['required', 'string', 'max:255'],
        ]);

        AssignFc::create($data);

        return redirect()->route('assign-fcs.index')->with('status', 'Assign FC added successfully.');
    }

    public function update(Request $request, AssignFc $assignFc): RedirectResponse
    {
        $data = $request->validate([
            'assign_fc' => ['required', 'string', 'max:255', Rule::unique('assign_fc', 'assign_fc')->ignore($assignFc)],
            'designation' => ['required', 'string', 'max:255'],
        ]);

        $assignFc->update($data);

        return redirect()->route('assign-fcs.index', ['view' => $assignFc->id])->with('status', 'Assign FC updated successfully.');
    }

    public function destroy(AssignFc $assignFc): RedirectResponse
    {
        if ($assignFc->clients()->exists()
            || $assignFc->workAgreements()->exists()
            || $assignFc->managedWorkAgreements()->exists()
            || $assignFc->consultedWorkAgreements()->exists()) {
            return redirect()->route('assign-fcs.index', ['view' => $assignFc->id])->withErrors(['assign_fc' => 'This FC cannot be deleted because it is related to client records.']);
        }

        $assignFc->delete();

        return redirect()->route('assign-fcs.index')->with('status', 'Assign FC deleted successfully.');
    }
}
