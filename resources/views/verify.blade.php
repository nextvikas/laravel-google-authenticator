@extends('authenticator::layouts.app')
@section('content')

<div class="text-center mb-4">
    <h2 class="mb-3">Verify Two-Factor Authentication</h2>
    <p class="lead">Enter the code from your Google Authenticator app.</p>
    <hr>
</div>

<form action="{{ route('authenticator.'.$role.'.verifypost') }}" method="POST">
    @csrf
    <div class="authenticator-form-group">
        <label for="code" class="form-label">Enter Your Google Authenticator Code</label>
        <input type="text" class="form-control" id="code" value="{{ old('code') }}" name="code" placeholder="******" required autofocus>
        @error('code')
            <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
        @enderror
    </div>

    <div class="authenticator-button-group">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Verify
        </button>

        @if($config['logout_route_name'])
            <a class="btn btn-secondary" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        @endif
    </div>
</form>

@if($config['logout_route_name'])
    <form id="logout-form" action="{{ route($config['logout_route_name']) }}" method="POST" style="display: none;">
        @csrf
    </form>
@endif

@endsection