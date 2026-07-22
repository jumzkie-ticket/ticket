<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientUser;
use App\Models\SecurityLevel;
use App\Models\Ticket;
use App\Models\TicketResolution;
use App\Models\TicketStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    /** @return array<string, string> */
    public static function statuses(): array
    {
        return [
            'new' => 'New',
            'open' => 'Open',
            'in-progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            'hold' => 'Hold',
            'no-helpdesk' => 'No Helpdesk',
            'no-maintenance' => 'No Maintenance',
            'pending-from-client' => 'Pending From Client',
            'pending-from-dev' => 'Pending From Dev',
            'pending-from-sales' => 'Pending From Sales',
            'pending-from-xti' => 'Pending From XTI',
        ];
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = (string) $request->input('status');
        $product = trim((string) $request->input('product'));
        $userId = $request->user()->id;
        $isAdmin = $this->userIsAdmin($request);

        $baseQuery = Ticket::query()
            ->with(['resolutions', 'securityLevel', 'ticketStatus'])
            ->unless($isAdmin, fn ($query) => $query->whereIn('client_id', ClientUser::query()
                ->select('client_id')
                ->where('user_id', $userId)));
        $tickets = (clone $baseQuery)
            ->when($status !== '' && array_key_exists($status, self::statuses()), fn ($query) => $query->whereRaw('LOWER(status) = ?', [$status]))
            ->when($product !== '', fn ($query) => $query->where('product_related', $product))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('issue_encountered', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('product_related', 'like', "%{$search}%")
                        ->orWhere('id', ctype_digit($search) ? (int) $search : 0);
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $selectedTicket = $request->filled('view')
            ? (clone $baseQuery)->find($request->integer('view'))
            : null;

        $counts = (clone $baseQuery)
            ->selectRaw("COUNT(*) AS total,
                SUM(CASE WHEN LOWER(status) IN ('new', 'open') THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN LOWER(status) = 'in-progress' THEN 1 ELSE 0 END) AS progress_count,
                SUM(CASE WHEN LOWER(status) = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
                SUM(CASE WHEN LOWER(status) = 'closed' THEN 1 ELSE 0 END) AS closed_count")
            ->first();

        $ticketStatusOptions = TicketStatus::query()->orderBy('id')->get();
        $statusCounts = (clone $baseQuery)
            ->selectRaw("LOWER(REPLACE(TRIM(status), '-', ' ')) AS normalized_status, COUNT(*) AS aggregate")
            ->groupByRaw("LOWER(REPLACE(TRIM(status), '-', ' '))")
            ->pluck('aggregate', 'normalized_status');
        $guideStatusOptions = $ticketStatusOptions
            ->unique(fn (TicketStatus $ticketStatus): string => mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status))))
            ->values();
        $statusGuide = collect([[
            'label' => 'Total Tickets',
            'value' => (int) $counts->total,
            'icon' => 'icon-ticket',
            'palette' => ['light' => '#5c54f4', 'dark' => '#8f88ff'],
        ]])->concat($guideStatusOptions->map(function (TicketStatus $ticketStatus) use ($statusCounts): array {
            $statusKey = strtolower(str_replace(' ', '-', $ticketStatus->status));
            $normalizedStatus = mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status)));

            return [
                'label' => self::statuses()[$statusKey] ?? ucwords(str_replace('-', ' ', $ticketStatus->status)),
                'value' => (int) ($statusCounts[$normalizedStatus] ?? 0),
                'icon' => match ($statusKey) {
                    'resolved' => 'icon-check-circle',
                    'closed', 'no-helpdesk', 'no-maintenance' => 'icon-x-square',
                    'in-progress', 'hold', 'pending-from-client', 'pending-from-dev', 'pending-from-sales', 'pending-from-xti' => 'icon-clock',
                    default => 'icon-inbox',
                },
                'palette' => $ticketStatus->palette(),
            ];
        }));
        $representedStatuses = $guideStatusOptions
            ->map(fn (TicketStatus $ticketStatus): string => mb_strtolower(str_replace('-', ' ', trim($ticketStatus->status))));
        $unassignedCount = (int) $statusCounts->except($representedStatuses->all())->sum();

        if ($unassignedCount > 0) {
            $statusGuide->push([
                'label' => 'Unassigned Status',
                'value' => $unassignedCount,
                'icon' => 'icon-help',
                'palette' => ['light' => '#64748b', 'dark' => '#cbd5e1'],
            ]);
        }

        return view('tickets.index', [
            'analytics' => [
                'total' => (int) $counts->total,
                'open' => (int) $counts->open_count,
                'in_progress' => (int) $counts->progress_count,
                'resolved' => (int) $counts->resolved_count,
                'closed' => (int) $counts->closed_count,
            ],
            'products' => (clone $baseQuery)->whereNotNull('product_related')->where('product_related', '!=', '')->distinct()->orderBy('product_related')->pluck('product_related'),
            'recentTickets' => (clone $baseQuery)->latest('updated_at')->limit(4)->get(),
            'selectedProduct' => $product,
            'securityLevels' => SecurityLevel::query()->orderBy('level_no')->get(),
            'ticketStatusOptions' => $ticketStatusOptions,
            'statusGuide' => $statusGuide,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedTicket' => $selectedTicket,
            'statuses' => self::statuses(),
            'tickets' => $tickets,
        ]);
    }

    public function create(): View
    {
        $clients = Client::query()
            ->with('sapProducts:id,sap_product')
            ->where('status', 'active')
            ->orderBy('company_name')
            ->get([
                'id', 'company_name', 'contact_person', 'email_address',
                'contact_country_code', 'contact_number',
                'version_number', 'patch_or_fp', 'db_version',
            ]);

        $clientLookup = $clients->map(fn (Client $client): array => [
            'id' => $client->id,
            'company_name' => $client->company_name,
            'contact_person' => $client->contact_person,
            'email_address' => $client->email_address,
            'contact_number' => trim($client->contact_country_code.' '.$client->contact_number),
            'product_related' => $client->sapProducts->pluck('sap_product')->join(', '),
            'software_version' => $client->version_number ?? '',
            'patch_or_fp' => $client->patch_or_fp ?? '',
            'database_version' => $client->db_version ?? '',
        ])->values();

        return view('tickets.create', compact('clientLookup'));
    }

    public function storeResolution(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($this->userCanAccessTicket($request, $ticket), 403);
        abort_if($this->userIsCustomer($request), 403);

        $data = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->resolutions()->create($data);

        return redirect()->route('tickets.index')->with('status', 'Resolution step added successfully.');
    }

    public function viewAttachment(Request $request, Ticket $ticket): BinaryFileResponse
    {
        abort_unless($this->userCanAccessTicket($request, $ticket), 403);
        abort_unless($ticket->attachment && Storage::disk('local')->exists($ticket->attachment), 404);

        return response()->file(Storage::disk('local')->path($ticket->attachment), [
            'Content-Disposition' => 'inline; filename="'.str_replace('"', '', $ticket->attachment_original_name ?: 'attachment').'"',
        ]);
    }

    public function updateResolution(Request $request, Ticket $ticket, TicketResolution $resolution): RedirectResponse
    {
        abort_unless($this->userCanAccessTicket($request, $ticket), 403);
        abort_if($this->userIsCustomer($request), 403);
        abort_unless($resolution->ticket_id === $ticket->id, 404);

        $resolution->update($request->validate([
            'date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:5000'],
        ]));

        return redirect()->route('tickets.index')->with('status', 'Resolution step updated successfully.');
    }

    public function updateClassification(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_unless($this->userCanAccessTicket($request, $ticket), 403);
        abort_if($this->userIsCustomer($request), 403);

        $data = $request->validate([
            'security_level_id' => ['nullable', 'integer', Rule::exists('security_level', 'id')],
            'ticket_status_id' => ['required', 'integer', Rule::exists('ticket_status', 'id')],
        ]);

        $ticketStatus = TicketStatus::findOrFail($data['ticket_status_id']);
        $ticket->update(array_merge($data, ['status' => $ticketStatus->status]));

        return redirect()->route('tickets.index')->with('status', 'Ticket details updated successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')->where('status', 'active')],
            'issue_encountered' => ['required', 'string', 'max:255'],
            'scenario' => ['required', 'string', 'max:5000'],
            'expected_result' => ['required', 'string', 'max:5000'],
            'full_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'other_information' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip', 'max:10240'],
        ]);

        $client = Client::query()->with('sapProducts:id,sap_product')->findOrFail($data['client_id']);
        $newTicketStatus = TicketStatus::query()
            ->whereKey(17)
            ->whereRaw('LOWER(status) = ?', ['new'])
            ->firstOrFail();
        $attachment = $data['attachment'] ?? null;
        unset($data['attachment']);

        if ($attachment) {
            $data['attachment'] = $attachment->store('ticket-attachments');
            $data['attachment_original_name'] = $attachment->getClientOriginalName();
        }

        Ticket::create(array_merge($data, [
            'company_name' => $client->company_name,
            'contact_person' => $client->contact_person,
            'email_address' => $client->email_address,
            'contact_number' => trim($client->contact_country_code.' '.$client->contact_number),
            'product_related' => $client->sapProducts->pluck('sap_product')->join(', '),
            'software_version' => $client->version_number,
            'patch_or_fp' => $client->patch_or_fp,
            'database_version' => $client->db_version,
            'created_by' => $request->user()->id,
            'status' => $newTicketStatus->status,
            'ticket_status_id' => $newTicketStatus->id,
        ]));

        return redirect()->route('tickets.create')->with('status', 'Your ticket has been created successfully.');
    }

    private function userCanAccessTicket(Request $request, Ticket $ticket): bool
    {
        if ($this->userIsAdmin($request)) {
            return true;
        }

        return ClientUser::query()
            ->where('user_id', $request->user()->id)
            ->where('client_id', $ticket->client_id)
            ->exists();
    }

    private function userIsAdmin(Request $request): bool
    {
        return $request->user()->roles()->where('slug', 'admin')->exists();
    }

    private function userIsCustomer(Request $request): bool
    {
        return $request->user()->roles()->where('slug', 'customer')->exists();
    }
}
