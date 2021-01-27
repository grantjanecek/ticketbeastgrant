<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use App\Models\AttendeeMessage;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Support\Facades\Auth;

class ConcertMessagesController extends Controller
{
    public function create($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);
        return view('backstage.concert-messages.create', compact('concert'));
    }

    public function store($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $this->validate(request(), [
            'subject' => ['required'],
            'message' => ['required'],
        ]);

        $message = $concert->attendeeMessages()->create(request([
            'subject',
            'message'
        ]));

        SendAttendeeMessage::dispatch($message);

        return redirect()->route('backstage.message-concert.create', $concert)
            ->with('flash', 'Message Sent!');
    }
}
