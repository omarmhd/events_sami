@extends('layouts.dashboard')

@section('dashboard_title', 'Admin Dashboard')

@section('menu')
<nav class="nav flex-column gap-2">
    <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a class="nav-link text-white" href="{{ route('admin.users.index') }}">Users</a>
    <a class="nav-link text-white" href="{{ route('admin.projects.index') }}">Projects</a>
</nav>
@endsection
