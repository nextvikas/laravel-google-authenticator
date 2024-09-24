@extends(Config('authenticator.'.$role.'.main_layout'))
@section('content')

<div class="row justify-content-center mt-3">
    <div class="col-md-4 col-md-offset-4">
        {{ Form::open(array('url' => route('authenticator.'.$role.'.verify'), 'method' => 'POST')) }}
        <div class="form-group @error('comment') has-error @enderror">
            <label>Enter Your Google Authenticator Code</label>

            <input type="text" class="form-control" value="{{ old('code') }}" name="code" placeholder="******">
            @error('code')
            <div class="text-danger" role="alert">{!! $message !!}</div>
            @enderror
        </div>

        <x-adminlte-button type="submit" label="Verify" theme="primary" icon="fa fa-key" />

        @if(config('authenticator.'.$role.'.logout_route_name'))
        <a class="btn btn-info btn-flat float-right" href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-fw fa-power-off"></i>
            Logout
        </a>
        @endif


        {{ Form::close() }}

        @if(config('authenticator.'.$role.'.logout_route_name'))
        <form id="logout-form" action="{{ route(config('authenticator.'.$role.'.logout_route_name')) }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        @endif

    </div>
</div>




@endsection