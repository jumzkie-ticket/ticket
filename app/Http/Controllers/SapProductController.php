<?php

namespace App\Http\Controllers;

use App\Models\SapProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SapProductController extends Controller
{
    public function index(Request $request): View
    {
        $sapProducts = SapProduct::query()
            ->withCount('clients')
            ->orderBy('sap_product')
            ->paginate(50)
            ->withQueryString();

        $selectedProduct = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedProduct = SapProduct::query()
                ->withCount('clients')
                ->find($request->integer('view'));
            $panelMode = $selectedProduct ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedProduct = SapProduct::query()
                ->withCount('clients')
                ->find($request->integer('edit'));
            $panelMode = $selectedProduct ? 'edit' : null;
        }

        $analytics = [
            'total' => SapProduct::query()->count(),
            'in_use' => SapProduct::query()->whereHas('clients')->count(),
            'unused' => SapProduct::query()->whereDoesntHave('clients')->count(),
            'related_clients' => DB::table('client_sap_product')->count(),
        ];

        return view('master-data.sap-products', compact('analytics', 'panelMode', 'sapProducts', 'selectedProduct'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sap_product' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sap_product'),
            ],
        ]);

        SapProduct::create($data);

        return redirect()
            ->route('sap-products.index')
            ->with('status', 'Product added successfully.');
    }

    public function update(Request $request, SapProduct $sapProduct): RedirectResponse
    {
        $data = $request->validate([
            'sap_product' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sap_product')->ignore($sapProduct),
            ],
        ]);

        $sapProduct->update($data);

        return redirect()
            ->route('sap-products.index', ['view' => $sapProduct->id])
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(SapProduct $sapProduct): RedirectResponse
    {
        if ($sapProduct->clients()->exists()) {
            return redirect()
                ->route('sap-products.index', ['view' => $sapProduct->id])
                ->withErrors(['sap_product' => 'This product cannot be deleted because it is related to client records.']);
        }

        $sapProduct->delete();

        return redirect()
            ->route('sap-products.index')
            ->with('status', 'Product deleted successfully.');
    }
}
