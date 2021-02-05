<?php

namespace App\Http\Controllers\Backstage;

use Carbon\Carbon;
use App\Models\Concert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NullFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConcertsController extends Controller
{
    public function create()
    {
        return view('backstage.concerts.create');
    }

    public function edit($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        return view('backstage.concerts.edit', compact('concert'));
    }

    public function update($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        abort_if($concert->isPublished(), 403);

        $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
        ]);

        $concert->update([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'additional_information' => request('additional_information'),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => (int)request('ticket_quantity'),
        ]);

        return redirect()->route('backstage.concerts.index');
    }

    public function store()
    {
        ray(request()->file('poster_image'));

        $this->validate(request(), [
            'title' => ['required'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:g:ia'],
            'venue' => ['required'],
            'venue_address' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'zip' => ['required'],
            'ticket_price' => ['required', 'numeric', 'min:5'],
            'ticket_quantity' => ['required', 'numeric', 'min:1'],
            'poster_image' => ['nullable', 'image', Rule::dimensions()->minWidth(600)->ratio(8.5/11)],
        ]);

        $concert = Auth::user()->concerts()->create([
            'title' => request('title'),
            'subtitle' => request('subtitle'),
            'additional_information' => request('additional_information'),
            'date' => Carbon::parse(vsprintf('%s %s', [
                request('date'),
                request('time'),
            ])),
            'venue' => request('venue'),
            'venue_address' => request('venue_address'),
            'city' => request('city'),
            'state' => request('state'),
            'zip' => request('zip'),
            'ticket_price' => request('ticket_price') * 100,
            'ticket_quantity' => (int)request('ticket_quantity'),
            'poster_image_path' => request('poster_image', new NullFile)->store('posters', 'public'),
        ]);

        return redirect()->route('backstage.concerts.index');
    }

    public function index()
    {
        $concerts = Auth::user()->concerts;

        return view('backstage.concerts.index', [
            'concerts' => $concerts,
            'publishedConcerts' => Auth::user()->concerts->filter->isPublished(),
            'unpublishedConcerts' => Auth::user()->concerts->reject->isPublished(),
        ]);
    }
}
