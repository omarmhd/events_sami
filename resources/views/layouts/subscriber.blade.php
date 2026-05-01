@extends('layouts.dashboard')

@section('dashboard_title', 'Subscriber Dashboard')

@section('menu')
<nav class="nav flex-column gap-2">
    <a class="nav-link text-white" href="{{ route('subscriber.dashboard') }}">Dashboard</a>
    <a class="nav-link text-white" href="{{ route('subscriber.events.index') }}">My Events</a>
    <a class="nav-link text-white" href="{{ route('subscriber.settings') }}">Settings</a>
</nav>
@endsection
