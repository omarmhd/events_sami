<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('subscriber.panel.dashboard');
    }
}


