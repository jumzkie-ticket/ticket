<?php

namespace App\Http\Controllers;

use App\Models\TicketStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketStatusController extends Controller
{
    public function index(Request $request): View
    {
        $ticketStatuses = TicketStatus::query()->orderBy('status')->paginate(25)->withQueryString();
        $selectedTicketStatus = $request->filled('edit') ? TicketStatus::find($request->integer('edit')) : null;

        return view('setup.ticket-statuses', compact('ticketStatuses', 'selectedTicketStatus'));
    }

    public function store(Request $request): RedirectResponse
    {
        TicketStatus::create($this->validated($request));
        return redirect()->route('ticket-statuses.index')->with('status', 'Ticket status added successfully.');
    }

    public function update(Request $request, TicketStatus $ticketStatus): RedirectResponse
    {
        $ticketStatus->update($this->validated($request, $ticketStatus));
        return redirect()->route('ticket-statuses.index')->with('status', 'Ticket status updated successfully.');
    }

    public function destroy(TicketStatus $ticketStatus): RedirectResponse
    {
        if ($ticketStatus->tickets()->exists()) {
            return redirect()->route('ticket-statuses.index')->withErrors(['status' => 'This status is assigned to a ticket and cannot be deleted.']);
        }

        $ticketStatus->delete();
        return redirect()->route('ticket-statuses.index')->with('status', 'Ticket status deleted successfully.');
    }

    private function validated(Request $request, ?TicketStatus $ticketStatus = null): array
    {
        return $request->validate([
            'status' => ['required', 'string', 'max:100', Rule::unique('ticket_status', 'status')->ignore($ticketStatus)],
        ]);
    }
}
