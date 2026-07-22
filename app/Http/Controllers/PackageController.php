<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $packages = Package::query()
            ->orderBy('package')
            ->paginate(50)
            ->withQueryString();

        $selectedPackage = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedPackage = Package::find($request->integer('view'));
            $panelMode = $selectedPackage ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedPackage = Package::find($request->integer('edit'));
            $panelMode = $selectedPackage ? 'edit' : null;
        }

        $analytics = [
            'total' => Package::query()->count(),
        ];

        return view('setup.packages', compact('analytics', 'packages', 'panelMode', 'selectedPackage'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package' => [
                'required',
                'string',
                'max:255',
                Rule::unique('packages', 'package'),
            ],
        ]);

        Package::create($data);

        return redirect()
            ->route('packages.index')
            ->with('status', 'Package added successfully.');
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $data = $request->validate([
            'package' => [
                'required',
                'string',
                'max:255',
                Rule::unique('packages', 'package')->ignore($package),
            ],
        ]);

        $package->update($data);

        return redirect()
            ->route('packages.index', ['view' => $package->id])
            ->with('status', 'Package updated successfully.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return redirect()
            ->route('packages.index')
            ->with('status', 'Package deleted successfully.');
    }
}
