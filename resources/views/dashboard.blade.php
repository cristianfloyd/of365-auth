@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">Dashboard</div>
    <div class="card-body">
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        
        <p>Has iniciado sesión correctamente, {{ auth()->user()->name }}!</p>
        
        @if(auth()->user()->avatar)
            <div class="mt-3">
                <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-circle" width="100">
            </div>
        @endif
    </div>
</div>
@endsection
