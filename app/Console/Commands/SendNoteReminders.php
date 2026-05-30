<?php

namespace App\Console\Commands;

use App\note;
use Illuminate\Console\Command;

class SendNoteReminders extends Command
{
    protected $signature = 'notes:send-reminders';
    protected $description = 'Notify admins of notes with due reminders';

    public function handle()
    {
        $notes = note::where('reminder_date', today())
            ->where('is_archived', false)
            ->with('user')
            ->get();

        foreach ($notes as $note) {
            \Log::info('Note reminder due', [
                'note_id' => $note->id,
                'user_id' => $note->user_id,
                'content' => substr($note->content, 0, 50),
            ]);
            // Extend here: send email or notification if needed
        }

        $this->info("Processed {$notes->count()} reminders.");
    }
}
