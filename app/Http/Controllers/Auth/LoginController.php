<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
        if(! Auth::attempt(request(['email', 'password']))) {
            return redirect('/login')->withErrors([
                'email' => 'Credentials do not match our records'
            ])->withInput(request(['email']));
        }
        return redirect('/backstage/concerts/new');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
