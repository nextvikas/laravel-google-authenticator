@extends('authenticator::layouts.app')
@section('content')

<div class="text-center mb-4">
    <h2 class="mb-3">Set Up Two-Factor Authentication</h2>
    <p class="lead">Scan the QR code with your Google Authenticator app.</p>
    <hr>
    <a href="{{ url('/') }}" class="btn btn-outline-secondary">
        <i class="fas fa-home"></i> Back to Home
    </a>
</div>

<form action="{{ route($fullrole.'.scanpost') }}" method="POST">
    @csrf
    <div class="authenticator-form-group text-center">
        @if(isset($qrCodeUrl))
            <img class="img-fluid" src="{{ $qrCodeUrl }}" alt="Scan this Google Authenticator QR Code" style="max-width:200px; height:auto; display:block; margin: 0 auto 1.5rem auto;"><br>
        @endif
    </div>

    <div class="authenticator-form-group">
        <label for="code" class="form-label">Enter Your Google Authenticator Code</label>
        <input type="text" class="form-control" id="code" value="{{ old('code') }}" name="code" placeholder="******" required autofocus>
        @error('code')
            <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
        @enderror
    </div>

    <div class="authenticator-button-group">
        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-check"></i> Verify
        </button>
    </div>
</form>

@endsection