@extends('layouts.app')

@section('content')

            <div class="panel panel-default">
                <div class="panel-body">
                      
                      <div class="flash-message">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                          @if(Session::has('alert-' . $msg))

                          <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                          @endif
                        @endforeach

                        @if(session('link'))

                          @if(session('link') == 'repair')
                            <h3><a href="{{ url('/repairs') }}">View all repairs</a></h3>

                          @elseif (session('link') == 'dashboard')
                            <h3><a href="{{ url('/home') }}">Back to dashboard</a></h3>
                          @elseif (session('link') == 'user')
                            <h3><a href="{{ url('/admin/users') }}">Back to manage users</a></h3>
                          @endif

                        @endif
            
                        @if ( session('product') )
                            <h3>User has been successfully created</h3>
                        @endif

                        @if(session('products') == 'added')
                          {!! Breadcrumbs::render('products') !!} 
                        @endif

                      </div> <!-- end .flash-message -->
                    
                </div>
            </div>

@endsection
