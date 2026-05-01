<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return view('subscriber.events.index');
    }

    public function show()
    {
        return view('subscriber.events.show');
    }

    public function create()
    {
        return view('subscriber.events.create');
    }

    public function edit()
    {
        return view('subscriber.events.edit');
    }
}


