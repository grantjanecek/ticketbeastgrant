<?php

namespace App\Models;

use App\Exceptions\NotEnoughTicketsException;
use App\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Concert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'date'
    ];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeUnpublished($query)
    {
        return $query->whereNull('published_at');
    }

    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);

        $this->addTickets($this->ticket_quantity);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price/100, 2);
    }

    public function orders()
    {
        return Order::whereIn('id', $this->tickets->pluck('order_id')->filter());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendeeMessages()
    {
        return $this->hasMany(AttendeeMessage::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create();
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function ticketsSold()
    {
        return $this->tickets()->sold()->count();
    }

    public function totalTickets()
    {
        return $this->tickets()->count();
    }

    public function percentSoldOut()
    {
        return $this->totalTickets() !== 0 ? number_format($this->ticketsSold() / $this->totalTickets() * 100) : 0;
    }

    public function revenueInDollars()
    {
        return $this->orders()->sum('amount') / 100;
    }

    public function findTickets($ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();

        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException();
        }

        return $tickets;
    }

    public function reserveTickets($quantity, $email)
    {
        $tickets = $this->findTickets($quantity)->each(fn ($ticket) => $ticket->reserve());

        return new Reservation($tickets, $email);
    }

    public function hasPoster()
    {
        return $this->poster_image_path !== null;
    }

    public function posterUrl()
    {
        return Storage::disk('public')->url($this->poster_image_path);
    }
}
