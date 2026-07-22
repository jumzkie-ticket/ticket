<?php

namespace App\Http\Controllers;

use App\Models\ContactSupportRequest;
use App\Models\SupportContactInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactSupportController extends Controller
{
    /**
     * @return array<string, string>
     */
    public static function priorities(): array
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'sap-business-one' => 'SAP Business One',
            'technical-support' => 'Technical Support',
            'account-access' => 'Account Access',
            'billing' => 'Billing',
            'general-inquiry' => 'General Inquiry',
        ];
    }

    public function index(): View
    {
        return view('contact-support', [
            'categories' => self::categories(),
            'priorities' => self::priorities(),
            'supportInfo' => SupportContactInfo::current(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(array_keys(self::priorities()))],
            'category' => ['required', Rule::in(array_keys(self::categories()))],
            'message' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,txt', 'max:10240'],
        ]);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('support-attachments', 'public');
        }

        unset($data['attachment']);

        ContactSupportRequest::create($data);

        return redirect()
            ->route('contact-support')
            ->with('status', 'Your support request has been submitted.');
    }
}
