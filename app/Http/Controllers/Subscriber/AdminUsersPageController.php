<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class AdminUsersPageController extends Controller
{
    public function __invoke()
    {
        return view('subscriber.panel.users-page');
    }
}


