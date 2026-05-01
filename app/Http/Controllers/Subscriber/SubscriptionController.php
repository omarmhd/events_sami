<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('subscriber.subscriptions.index');
    }

    public function details()
    {
        return view('subscriber.subscriptions.details');
    }
}


