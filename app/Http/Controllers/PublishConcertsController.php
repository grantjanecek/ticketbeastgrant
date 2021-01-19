<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublishConcertsController extends Controller
{
    public function store()
    {
        $this->validate(request(), [
            'concert_id' => ['required'],
        ]);
        
        $concert = Auth::user()->concerts()->findOrFail(request('concert_id'));

        abort_if($concert->isPublished(), 422);

        $concert->publish();

        return redirect('/backstage/concerts');
    }
}
