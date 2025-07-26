@extends('layouts.app')

@section('content')
<div class="container">
    @include('components/navigation')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                      
                      <div class="flash-message">
                          test api

                          {{ $token_expires }}
                      </div> <!-- end .flash-message -->
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

<script>
  $.ajax({

        'url' : 'https://login.microsoftonline.com/common/oauth2/v2.0/token/client_id=c33958e0-9c45-4a4e-b5e2-2d8566af7afa&redirect_uri=http://localhost:8000/onedrive/public/connect-api&client_secret=nouwMDS55%_exaECTF454)!&code={code}&grant_type=authorization_code',
        'type' : 'POST',
        'content-type' : 'application/x-www-form-urlencoded',
        'data' : {
            'client_id' : '',
            'redirect_uri' : 'http://localhost:8000/onedrive/public/connect-api',
            'client_secret' : '',
            'code' : '{{ $token }}',
            'grant_type' : ''
        },
        'success' : function(data) {              
            console.log( 'data ' + data);
        },
        'error' : function(request,error)
        {
            console.log('nothing');
        }

  });
</script>
@endsection
