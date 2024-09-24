@extends(Config('authenticator.'.$role.'.main_layout'))
@section('content')

<div class="row justify-content-center mt-3">
            <div class="col-md-6 col-md-offset-3 _amd">
                <p class="_ap">Identify yourself by scannning the QR code with Google Authenticator app</p>
                <hr>
                {{ Form::open(array('url' => route('authenticator.'.$role.'.scan'), 'method' => 'POST')) }}
                    <div class="_aform">


                        <div class="form-group @error('comment') has-error @enderror">

                        @if(isset($qrCodeUrl))
                            <img class="img-fluid" src="{{ $qrCodeUrl }}" alt="Verify this Google Authenticator"><br><br>
                        @endif



                            <label>Enter Your Google Authenticator Code</label>
                            <input type="text" class="form-control" value="{{ old('code') }}" name="code" placeholder="******">
                            @error('code')
                                <div class="text-danger" role="alert">{!! $message !!}</div>
                            @enderror
                        </div>

 
                            <x-adminlte-button type="submit" label="Verify" theme="primary" icon="fa fa-key"/>

                    </div>

               {{ Form::close() }}
            </div>
        </div>

@endsection