<?php

namespace App\Http\Controllers;

use App\Models\ProductDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProductDetailsController extends Controller
{
    public function index(Request $request): View
    {
        $productDetails = ProductDetail::query()
            ->orderBy('helpdesk_support_packages')
            ->get();

        $selectedProductDetail = null;
        $panelMode = null;

        if ($request->filled('view')) {
            $selectedProductDetail = ProductDetail::find($request->integer('view'));
            $panelMode = $selectedProductDetail ? 'view' : null;
        } elseif ($request->filled('edit')) {
            $selectedProductDetail = ProductDetail::find($request->integer('edit'));
            $panelMode = $selectedProductDetail ? 'edit' : null;
        }

        return view('setup.product-details', compact('panelMode', 'productDetails', 'selectedProductDetail'));
    }

    public function store(Request $request): RedirectResponse
    {
        ProductDetail::create($this->validatedProductDetail($request));

        return redirect()
            ->route('product-details')
            ->with('status', 'Product detail added successfully.');
    }

    public function update(Request $request, ProductDetail $productDetail): RedirectResponse
    {
        $productDetail->update($this->validatedProductDetail($request));

        return redirect()
            ->route('product-details', ['view' => $productDetail->id])
            ->with('status', 'Product detail updated successfully.');
    }

    public function destroy(ProductDetail $productDetail): RedirectResponse
    {
        $productDetail->delete();

        return redirect()
            ->route('product-details')
            ->with('status', 'Product detail deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProductDetail(Request $request): array
    {
        $this->ensureCurrencyFieldsAreNumeric($request);

        $request->merge([
            'helpdesk_support_fee' => $this->normalizeCurrency($request->input('helpdesk_support_fee')),
            'total_amount_vat_inc' => $this->normalizeCurrency($request->input('total_amount_vat_inc')),
        ]);

        return $request->validate([
            'helpdesk_support_packages' => ['required', 'string', 'max:255'],
            'man_days' => ['required', 'integer', 'min:0', 'max:9999'],
            'helpdesk_coverage_months' => ['required', 'integer', 'min:1', 'max:999'],
            'helpdesk_support_fee' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'total_amount_vat_inc' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
        ], [
            'man_days.integer' => 'Only numbers are allowed.',
            'man_days.numeric' => 'Only numbers are allowed.',
            'helpdesk_coverage_months.integer' => 'Only numbers are allowed.',
            'helpdesk_coverage_months.numeric' => 'Only numbers are allowed.',
            'helpdesk_support_fee.numeric' => 'Only numbers are allowed.',
            'total_amount_vat_inc.numeric' => 'Only numbers are allowed.',
        ]);
    }

    private function ensureCurrencyFieldsAreNumeric(Request $request): void
    {
        $errors = [];

        foreach (['helpdesk_support_fee', 'total_amount_vat_inc'] as $field) {
            if ($this->hasInvalidCurrencyCharacters($request->input($field))) {
                $errors[$field] = 'Only numbers are allowed.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function hasInvalidCurrencyCharacters(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $value = preg_replace('/^\s*PHP\s*/i', '', $value) ?? $value;

        return preg_match('/[^0-9.,\s]/', $value) === 1;
    }

    private function normalizeCurrency(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return preg_replace('/[^0-9.]/', '', $value);
    }
}
