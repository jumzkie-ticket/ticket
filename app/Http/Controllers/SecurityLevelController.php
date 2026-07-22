<?php

namespace App\Http\Controllers;

use App\Models\SecurityLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SecurityLevelController extends Controller
{
    public function index(Request $request): View
    {
        $securityLevels = SecurityLevel::query()->orderBy('level_no')->paginate(25)->withQueryString();
        $selectedSecurityLevel = $request->filled('edit') ? SecurityLevel::find($request->integer('edit')) : null;

        return view('setup.security-levels', compact('securityLevels', 'selectedSecurityLevel'));
    }

    public function store(Request $request): RedirectResponse
    {
        SecurityLevel::create($this->validated($request));

        return redirect()->route('security-levels.index')->with('status', 'Security level added successfully.');
    }

    public function update(Request $request, SecurityLevel $securityLevel): RedirectResponse
    {
        $securityLevel->update($this->validated($request, $securityLevel));

        return redirect()->route('security-levels.index')->with('status', 'Security level updated successfully.');
    }

    public function destroy(SecurityLevel $securityLevel): RedirectResponse
    {
        if ($securityLevel->tickets()->exists()) {
            return redirect()->route('security-levels.index')->withErrors(['security_level' => 'This security level is assigned to a ticket and cannot be deleted.']);
        }

        $securityLevel->delete();

        return redirect()->route('security-levels.index')->with('status', 'Security level deleted successfully.');
    }

    private function validated(Request $request, ?SecurityLevel $securityLevel = null): array
    {
        return $request->validate([
            'level_no' => ['required', 'string', 'max:40', Rule::unique('security_level', 'level_no')->ignore($securityLevel)],
            'description' => ['required', 'string', 'max:500'],
            'sla' => ['required', 'string', 'max:120'],
        ]);
    }
}
