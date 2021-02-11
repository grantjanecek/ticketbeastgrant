<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register()
    {
        $invitation = Invitation::findByCode(request('invitation_code'));

        abort_if($invitation->hasBeenUsed(), 404);

        request()->validate([
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
        ]);

        $user = User::create([
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);

        $invitation->update(['user_id' => $user->id]);

        Auth::login($user);

        return redirect('/backstage/concerts');
    }
}
