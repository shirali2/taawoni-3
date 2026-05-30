<?php

namespace App\Http\Controllers\User;

use App\note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserNoteController extends Controller
{
    private function requireUser()
    {
        if (!session()->has('id_user')) {
            abort(redirect('/'));
        }
    }

    public function index()
    {
        $this->requireUser();

        $notes = note::where('user_id', session('id_user'))
            ->approved()
            ->visibleToUser()
            ->notArchived()
            ->latest()
            ->get();

        return view('user.notes.index', compact('notes'));
    }

    public function markAsSeen(Request $request, $id)
    {
        $this->requireUser();

        $note = note::where('id', $id)
            ->where('user_id', session('id_user'))
            ->firstOrFail();

        if (is_null($note->seen_by_user_at)) {
            $note->update(['seen_by_user_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
