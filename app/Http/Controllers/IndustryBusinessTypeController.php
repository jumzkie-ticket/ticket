<?php

namespace App\Http\Controllers;

use App\Models\IndustryBusinessType;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndustryBusinessTypeController extends Controller
{
    public function index(Request $request): View
    {
        $industries = IndustryBusinessType::query()
            ->withCount('clients')
            ->orderBy('industry')
            ->paginate(50)
            ->withQueryString();

        $selectedIndustry = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedIndustry = IndustryBusinessType::query()
                ->withCount('clients')
                ->find($request->integer('view'));
            $panelMode = $selectedIndustry ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedIndustry = IndustryBusinessType::query()
                ->withCount('clients')
                ->find($request->integer('edit'));
            $panelMode = $selectedIndustry ? 'edit' : null;
        }

        $analytics = [
            'total' => IndustryBusinessType::query()->count(),
            'in_use' => IndustryBusinessType::query()->whereHas('clients')->count(),
            'unused' => IndustryBusinessType::query()->whereDoesntHave('clients')->count(),
            'related_clients' => Client::query()->whereNotNull('industry_business_type_id')->count(),
        ];

        return view('master-data.industry-business-types', compact('analytics', 'industries', 'panelMode', 'selectedIndustry'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'industry' => [
                'required',
                'string',
                'max:255',
                Rule::unique('industry_business_types', 'industry'),
            ],
        ]);

        IndustryBusinessType::create($data);

        return redirect()
            ->route('industry-business-types.index')
            ->with('status', 'Industry / Business Type added successfully.');
    }

    public function update(Request $request, IndustryBusinessType $industryBusinessType): RedirectResponse
    {
        $data = $request->validate([
            'industry' => [
                'required',
                'string',
                'max:255',
                Rule::unique('industry_business_types', 'industry')->ignore($industryBusinessType),
            ],
        ]);

        $industryBusinessType->update($data);

        return redirect()
            ->route('industry-business-types.index', ['view' => $industryBusinessType->id])
            ->with('status', 'Industry / Business Type updated successfully.');
    }

    public function destroy(IndustryBusinessType $industryBusinessType): RedirectResponse
    {
        if ($industryBusinessType->clients()->exists()) {
            return redirect()
                ->route('industry-business-types.index', ['view' => $industryBusinessType->id])
                ->withErrors(['industry' => 'This industry cannot be deleted because it is related to client records.']);
        }

        $industryBusinessType->delete();

        return redirect()
            ->route('industry-business-types.index')
            ->with('status', 'Industry / Business Type deleted successfully.');
    }
}
