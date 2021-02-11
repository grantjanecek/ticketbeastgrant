<?php

use App\Models\Invitation;
use App\Mail\InvitationEmail;
use App\Facades\InvitationCode;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('invite-promoter {email}', fn ($email) =>
    Invitation::create([
        'email' => $email,
        'code' => InvitationCode::generate()
    ])->send()
)->purpose('Invite a new promoter to create an account');
