<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function orders()
    {
        return $this->concert->orders();
    }

    public function withRecipients($callback)
    {
        $this->orders()->chunk(20, fn($orders) =>
            $callback($orders->pluck('email'))
        );
    }
}
