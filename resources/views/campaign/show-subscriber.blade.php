@extends('layouts.app')

@section('content')

    @if( Auth::user()->isAdmin() )

      <div class="panel panel-top">

          <div class="row">

              <div class="col-sm-6">

                {!! Breadcrumbs::render('settings') !!}

              </div>

              <div class="col-sm-6 text-right">
              
              </div>

          </div>

      </div> 

    @endif

    @include('components/flash')

    @component('components/panel')

       @slot('title')
          Removing a contact from the subscriber list
       @endslot

       @if( $subscriber )

         <div class="contact-list">

            <h2>{{ $subscriber->firstname }}  {{ $subscriber->lastname }}</h2>
            <p style="margin-bottom: 30px;">{{ $subscriber->email }}</p>
        
            {!! Form::open([
                 'method' => 'patch',
                 'route' => ['campaign.update', $subscriber->id]
                ]) !!}
                <button type="submit" class="btn-brand btn-brand-danger btn-brand-icon"><i class="btn-icon fa fa-close"></i> <span>unsubscribe</span></button>
            {!! Form::close() !!}

         </div>  

       @endif  
       

    @endcomponent

@endsection

