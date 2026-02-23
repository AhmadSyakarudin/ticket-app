<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:2000',
        ], [
            'content.required' => 'Komentar tidak boleh kosong.',
            'content.min' => 'Komentar minimal 3 karakter.',
            'content.max' => 'Komentar maksimal 2000 karakter.',
        ]);

        $cleanContent = strip_tags($validated['content']);


        // ⚠️ TEMPORARY: Hardcode user_id = 1 (demo user dari seeder)
        // TODO: Ganti dengan Auth::id() di Minggu 4 setelah implementasi Authentication
        // Contoh nanti: 'user_id' => Auth::id(),
        $comment = Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(), // TEMPORARY - akan diganti Auth::id() di Minggu 4
            'content' => $cleanContent,
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        
        if (Auth::id() !== $comment->user_id) {
            $isAdmin = Auth::user()->is_admin ?? false;
            
            if (!$isAdmin) {
                // Unauthorized - return 403 Forbidden
                abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
            }
        }
        

        // Simpan ticket untuk redirect
        $ticket = $comment->ticket;

        // ✅ DELETE COMMENT
        $comment->delete();

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Komentar berhasil dihapus!');
    }

    public function update(Request $request, Comment $comment): RedirectResponse
    {

        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }
        

        // ✅ VALIDASI
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:2000',
        ]);

        // ✅ SANITASI
        $cleanContent = strip_tags($validated['content']);

        // ✅ UPDATE
        $comment->update([
            'content' => $cleanContent,
        ]);

        return redirect()
            ->route('tickets.show', $comment->ticket)
            ->with('success', 'Komentar berhasil diperbarui!');
    }
}
