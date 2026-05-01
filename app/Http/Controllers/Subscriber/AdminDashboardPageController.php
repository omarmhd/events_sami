<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class AdminDashboardPageController extends Controller
{
    public function __invoke()
    {
        return view('subscriber.panel.dashboard-page');
    }
}


