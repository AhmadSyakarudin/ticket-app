<?php

namespace App\Http\Controllers;

use App\Models\XssLabComment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class XSSLabController extends Controller
{
    /**
     * Halaman index Lab XSS
     */
    public function index(): View
    {
        return view('xss-lab.index');
    }

    public function reflectedVulnerable(Request $request): View
    {
        $searchQuery = $request->input('q', '');

        return view('xss-lab.vulnerable.reflected', [
            'searchQuery' => $searchQuery,
        ]);
    }

    public function reflectedSecure(Request $request): View
    {
        $searchQuery = $request->input('q', '');

        return view('xss-lab.secure.reflected', [
            'searchQuery' => $searchQuery,
        ]);
    }


    public function storedVulnerable(): View
    {
        $comments = XssLabComment::orderBy('created_at', 'desc')->get();
        $ticket = Ticket::first();

        return view('xss-lab.vulnerable.stored', [
            'comments' => $comments,
            'ticket' => $ticket,
        ]);
    }

    public function storedVulnerableStore(Request $request): RedirectResponse
    {
        XssLabComment::create([
            'ticket_id' => $request->input('ticket_id') ?? 1,
            'author_name' => $request->input('author_name'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('xss-lab.stored.vulnerable')
            ->with('success', 'Komentar berhasil ditambahkan!');
    }


    public function storedSecure(): View
    {
        $comments = XssLabComment::orderBy('created_at', 'desc')->get();
        $ticket = Ticket::first();

        return view('xss-lab.secure.stored', [
            'comments' => $comments,
            'ticket' => $ticket,
        ]);
    }

    public function storedSecureStore(Request $request): RedirectResponse
    {
        // ✅ SECURE: Validasi input dengan proper rules
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'author_name' => 'required|string|max:100',
            'content' => 'required|string|max:1000',
        ]);

        $validated['author_name'] = strip_tags($validated['author_name']);
        $validated['content'] = strip_tags($validated['content']);

        return redirect()->route('xss-lab.stored.secure')
            ->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function domVulnerable(): View
    {
        return view('xss-lab.vulnerable.dom-based');
    }

    public function domSecure(): View
    {
        return view('xss-lab.secure.dom-based');
    }

    public function resetComments(): RedirectResponse
    {
        XssLabComment::truncate();

        return redirect()->route('xss-lab.index')
            ->with('success', 'Semua komentar XSS Lab berhasil dihapus!');
    }
}
