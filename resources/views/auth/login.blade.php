@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Iniciar sesión</div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="text-center mt-3">
                    <a href="{{ route('auth.microsoft') }}" class="btn btn-primary">
                        <i class="fab fa-microsoft me-2"></i> Iniciar sesión con Office 365
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
