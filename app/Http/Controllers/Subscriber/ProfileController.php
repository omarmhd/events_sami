<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        return view('subscriber.panel.profile');
    }
}


