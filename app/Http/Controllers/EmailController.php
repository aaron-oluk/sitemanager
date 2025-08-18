<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmailController extends Controller
{
    public function index(): View
    {
        $emails = Email::latest()->paginate(15);
        return view('emails.index', compact('emails'));
    }

    public function create(): View
    {
        return view('emails.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_address' => 'required|email|unique:emails',
            'provider' => 'required|string|max:255',
            'hosting_plan' => 'nullable|string|max:255',
            'monthly_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,suspended,cancelled',
            'notes' => 'nullable|string',
            'associated_website' => 'nullable|string|max:255',
        ]);

        Email::create($validated);

        return redirect()->route('emails.index')->with('success', 'Email account created successfully!');
    }

    public function show(Email $email): View
    {
        return view('emails.show', compact('email'));
    }

    public function edit(Email $email): View
    {
        return view('emails.edit', compact('email'));
    }

    public function update(Request $request, Email $email): RedirectResponse
    {
        $validated = $request->validate([
            'email_address' => 'required|email|unique:emails,email_address,' . $email->id,
            'provider' => 'required|string|max:255',
            'hosting_plan' => 'nullable|string|max:255',
            'monthly_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,suspended,cancelled',
            'notes' => 'nullable|string',
            'associated_website' => 'nullable|string|max:255',
        ]);

        $email->update($validated);

        return redirect()->route('emails.index')->with('success', 'Email account updated successfully!');
    }

    public function destroy(Email $email): RedirectResponse
    {
        $email->delete();

        return redirect()->route('emails.index')->with('success', 'Email account deleted successfully!');
    }
}
