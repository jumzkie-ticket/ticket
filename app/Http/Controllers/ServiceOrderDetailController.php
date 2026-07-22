<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\IndustryBusinessType;
use App\Models\Package;
use App\Models\SapProduct;
use App\Models\ServiceOrderDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServiceOrderDetailController extends Controller
{
    public function index(): View
    {
        return view('customer-management.service-order-details', $this->serviceOrderFormData());
    }

    public function edit(ServiceOrderDetail $serviceOrder): View
    {
        $serviceOrder->load(['industryBusinessType', 'sapProducts', 'packages']);

        return view('customer-management.service-order-details', $this->serviceOrderFormData($serviceOrder));
    }

    public function show(ServiceOrderDetail $serviceOrder): View
    {
        $serviceOrder->load(['client', 'industryBusinessType', 'sapProducts', 'packages']);

        return view('customer-management.service-order-show', compact('serviceOrder'));
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceOrderFormData(?ServiceOrderDetail $serviceOrder = null): array
    {
        $clients = Client::query()
            ->with(['industryBusinessType:id,industry', 'sapProducts:id,sap_product'])
            ->orderBy('company_name')
            ->get();

        $clientData = $clients->mapWithKeys(fn (Client $client): array => [
            (string) $client->id => [
                'industry_business_type_id' => $client->industry_business_type_id,
                'industry_name' => $client->industryBusinessType?->industry ?? '',
                'version_number' => $client->version_number ?? '',
                'patch_or_fp' => $client->patch_or_fp ?? '',
                'unused_man_days' => $this->previousYearUnusedManDays($client->id),
                'products' => $client->sapProducts->map(fn (SapProduct $product): array => [
                    'id' => $product->id,
                    'name' => $product->sap_product,
                ])->values(),
            ],
        ]);

        return [
            'clients' => $clients,
            'clientData' => $clientData,
            'industries' => IndustryBusinessType::query()->orderBy('industry')->get(),
            'sapProducts' => SapProduct::query()->orderBy('sap_product')->get(),
            'packages' => Package::query()->orderBy('package')->get(),
            'licenseTypes' => ['Professional', 'Limited', 'Indirect', 'Starter', 'MSSQL'],
            'serviceOrder' => $serviceOrder,
        ];
    }

    public function detail(Request $request): View
    {
        $filters = [
            'client_id' => $request->input('client_id'),
            'industry_business_type_id' => $request->input('industry_business_type_id'),
            'sap_product_id' => $request->input('sap_product_id'),
            'software_version' => $request->input('software_version'),
            'patch_or_fp' => $request->input('patch_or_fp'),
            'year' => $request->input('year'),
        ];

        $years = ServiceOrderDetail::query()
            ->select(['support_start_date', 'support_end_date', 'created_at'])
            ->get()
            ->flatMap(fn (ServiceOrderDetail $order) => [
                $order->support_start_date?->format('Y'),
                $order->support_end_date?->format('Y'),
                $order->created_at?->format('Y'),
            ])
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $filters['year'] = $filters['year'] ?: ($years->first() ?: now()->format('Y'));
        $perPage = min(max((int) $request->input('per_page', 10), 5), 25);
        $serviceOrdersQuery = ServiceOrderDetail::query()
            ->with([
                'client:id,company_name',
                'industryBusinessType:id,industry',
                'sapProducts:id,sap_product',
                'packages:id,package',
            ]);
        $this->applyServiceOrderFilters($serviceOrdersQuery, $filters);
        $serviceOrders = $serviceOrdersQuery
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $analyticsQuery = ServiceOrderDetail::query();
        $this->applyYearFilter($analyticsQuery, (string) $filters['year']);

        return view('customer-management.service-order-detail', [
            'serviceOrders' => $serviceOrders,
            'clients' => Client::query()->orderBy('company_name')->get(),
            'industries' => IndustryBusinessType::query()->orderBy('industry')->get(),
            'sapProducts' => SapProduct::query()->orderBy('sap_product')->get(),
            'filters' => $filters,
            'years' => $years->isNotEmpty() ? $years : collect([now()->format('Y')]),
            'analytics' => $this->serviceOrderAnalytics($analyticsQuery),
            'sapProductsById' => [],
            'softwareVersions' => ServiceOrderDetail::query()->whereNotNull('software_version')->distinct()->orderBy('software_version')->pluck('software_version'),
            'patchOrFps' => ServiceOrderDetail::query()->whereNotNull('patch_or_fp')->distinct()->orderBy('patch_or_fp')->pluck('patch_or_fp'),
        ]);
    }

    private function applyServiceOrderFilters(Builder $query, array $filters): void
    {
        $query
            ->when($filters['client_id'] ?? null, fn (Builder $q, string $id) => $q->where('client_id', $id))
            ->when($filters['industry_business_type_id'] ?? null, fn (Builder $q, string $id) => $q->where('industry_business_type_id', $id))
            ->when($filters['sap_product_id'] ?? null, fn (Builder $q, string $id) => $q->whereHas('sapProducts', fn (Builder $products) => $products->where('products.id', $id)))
            ->when($filters['software_version'] ?? null, fn (Builder $q, string $version) => $q->where('software_version', $version))
            ->when($filters['patch_or_fp'] ?? null, fn (Builder $q, string $patch) => $q->where('patch_or_fp', $patch));

        $this->applyYearFilter($query, (string) ($filters['year'] ?? ''));
    }

    private function applyYearFilter(Builder $query, string $year): void
    {
        if ($year === '') {
            return;
        }

        $query->where(function (Builder $dates) use ($year): void {
            $dates->whereYear('support_start_date', $year)
                ->orWhereYear('support_end_date', $year)
                ->orWhereYear('created_at', $year);
        });
    }

    private function serviceOrderAnalytics(Builder $query): array
    {
        $total = (clone $query)->count();
        $onHold = (clone $query)->whereRaw("LOWER(COALESCE(support_inclusion, '')) LIKE ?", ['%hold%'])->count();
        $completed = (clone $query)
            ->whereRaw("LOWER(COALESCE(support_inclusion, '')) NOT LIKE ?", ['%hold%'])
            ->whereRaw('(COALESCE(man_days, 0) + COALESCE(unused_man_days, 0)) > 0')
            ->whereRaw('(COALESCE(man_days, 0) + COALESCE(unused_man_days, 0) - COALESCE(used_man_days, 0)) <= 0')
            ->count();

        return [
            'total' => $total,
            'active' => max(0, $total - $completed - $onHold),
            'completed' => $completed,
            'on_hold' => $onHold,
        ];
    }

    /**
     * @return array<int, int>
     */
    private function serviceOrderProductIds(ServiceOrderDetail $order): array
    {
        $pivotIds = $order->relationLoaded('sapProducts')
            ? $order->sapProducts->pluck('id')->all()
            : [];

        return array_values(array_unique(array_map('intval', array_filter($pivotIds))));
    }

    private function serviceOrderStatus(ServiceOrderDetail $order): string
    {
        $supportInclusion = strtolower((string) $order->support_inclusion);

        if (str_contains($supportInclusion, 'hold')) {
            return 'On Hold';
        }

        $manDays = max((int) ($order->man_days ?? 0), 0) + max((int) ($order->unused_man_days ?? 0), 0);
        $remainingManDays = $order->remaining_man_days;

        if ($manDays > 0 && $remainingManDays === 0) {
            return 'Completed';
        }

        return 'Active';
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedServiceOrderData($request);
        $productIds = $data['sap_product_ids'];
        $packageIds = $data['package_ids'];
        $attachment = $data['attach_service_order'] ?? null;
        unset($data['sap_product_ids'], $data['package_ids']);
        unset($data['attach_service_order']);
        if ($attachment) {
            $data['attach_service_order'] = $attachment->store('service-order-attachments');
            $data['attach_service_order_original_name'] = $attachment->getClientOriginalName();
        }
        $serviceOrder = ServiceOrderDetail::create($data);

        $serviceOrder->packages()->sync($packageIds);
        $serviceOrder->sapProducts()->sync($productIds);

        return redirect()
            ->route('service-order-details.index')
            ->with('status', 'Service order saved successfully.');
    }

    public function update(Request $request, ServiceOrderDetail $serviceOrder): RedirectResponse
    {
        $data = $this->validatedServiceOrderData($request);
        $productIds = $data['sap_product_ids'];
        $packageIds = $data['package_ids'];
        $attachment = $data['attach_service_order'] ?? null;
        unset($data['sap_product_ids'], $data['package_ids']);
        unset($data['attach_service_order']);

        if ($attachment) {
            if ($serviceOrder->attach_service_order) {
                Storage::disk('local')->delete($serviceOrder->attach_service_order);
            }
            $data['attach_service_order'] = $attachment->store('service-order-attachments');
            $data['attach_service_order_original_name'] = $attachment->getClientOriginalName();
        }

        $serviceOrder->update($data);
        $serviceOrder->packages()->sync($packageIds);
        $serviceOrder->sapProducts()->sync($productIds);

        return redirect()
            ->route('service-order-details.detail', ['service_order_id' => $serviceOrder->id])
            ->with('status', 'Service order updated successfully.');
    }

    public function viewAttachment(ServiceOrderDetail $serviceOrder): BinaryFileResponse
    {
        abort_unless($serviceOrder->attach_service_order && Storage::disk('local')->exists($serviceOrder->attach_service_order), 404);

        return response()->file(Storage::disk('local')->path($serviceOrder->attach_service_order), [
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $serviceOrder->attach_service_order_original_name ?: 'service-order-attachment').'"',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedServiceOrderData(Request $request): array
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'industry_business_type_id' => ['required', 'integer', 'exists:industry_business_types,id'],
            'package_ids' => ['required', 'array', 'min:1'],
            'package_ids.*' => ['integer', 'exists:packages,id'],
            'sap_product_ids' => ['required', 'array'],
            'sap_product_ids.*' => ['integer', 'exists:products,id'],
            'software_version' => ['required', 'string', 'max:100'],
            'patch_or_fp' => ['required', 'string', 'max:100'],
            'support_start_date' => ['required', 'date'],
            'support_end_date' => ['required', 'date', 'after:support_start_date'],
            'cas_accredited' => ['required', 'boolean'],
            'support_inclusion' => ['required', 'string', 'max:255'],
            'man_days' => ['required', 'integer', 'min:0'],
            'unused_man_days' => ['nullable', 'integer', 'min:0'],
            'used_man_days' => ['required', 'integer', 'min:0'],
            'license_type' => ['nullable', 'string', 'in:Professional,Limited,Indirect,Starter,MSSQL'],
            'professional' => ['nullable', 'integer', 'min:0'],
            'limited' => ['nullable', 'integer', 'min:0'],
            'indirect' => ['nullable', 'integer', 'min:0'],
            'starter' => ['nullable', 'integer', 'min:0'],
            'mssql' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attach_service_order' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip', 'max:10240'],
        ]);

        $data['cas_accredited'] = (bool) ($request->boolean('cas_accredited') ?? false);
        $data['package_ids'] = array_values(array_unique(array_map('intval', (array) ($data['package_ids'] ?? []))));
        $packageNames = Package::query()
            ->whereIn('id', $data['package_ids'])
            ->pluck('package')
            ->map(fn (string $name): string => mb_strtolower($name));

        if ($packageNames->contains(fn (string $name): bool => str_contains($name, 'premium helpdesk support plan'))) {
            $data['man_days'] = 12;
        } elseif ($packageNames->contains(fn (string $name): bool => str_contains($name, 'standard helpdesk support plan'))) {
            $data['man_days'] = 6;
        } elseif ($packageNames->contains(fn (string $name): bool => str_contains($name, 'basic helpdesk support plan'))) {
            $data['man_days'] = 0;
        }

        $data['sap_product_ids'] = Client::query()
            ->findOrFail($data['client_id'])
            ->sapProducts()
            ->get(['products.id'])
            ->pluck('id')
            ->map(fn (int $id): int => $id)
            ->values()
            ->all();

        if ($data['sap_product_ids'] === []) {
            throw ValidationException::withMessages([
                'sap_product_ids' => 'The selected client does not have any registered products.',
            ]);
        }
        $data['professional'] = (int) ($data['professional'] ?? 0);
        $data['limited'] = (int) ($data['limited'] ?? 0);
        $data['indirect'] = (int) ($data['indirect'] ?? 0);
        $data['starter'] = (int) ($data['starter'] ?? 0);
        $data['unused_man_days'] = $this->previousYearUnusedManDays((int) $data['client_id']);
        $data['mssql'] = ($data['professional'] + $data['limited'] + $data['starter']);

        return $data;
    }

    private function previousYearUnusedManDays(int $clientId): int
    {
        $previousOrder = ServiceOrderDetail::query()
            ->where('client_id', $clientId)
            ->whereYear('support_end_date', now()->subYear()->year)
            ->latest('support_end_date')
            ->latest('id')
            ->first(['man_days', 'unused_man_days', 'used_man_days']);

        return $previousOrder?->remaining_man_days ?? 0;
    }
}
