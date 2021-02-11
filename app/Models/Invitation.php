<?php

namespace App\Models;

use App\Models\User;
use App\Mail\InvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasBeenUsed()
    {
        return $this->user_id !== null;
    }

    public static function findByCode($code)
    {
        return self::where('code', $code)->firstOrFail();
    }

    public function send()
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }
}
