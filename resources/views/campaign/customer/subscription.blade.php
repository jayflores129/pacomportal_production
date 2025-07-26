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
          Subscription
       @endslot

       {!! Form::open([
           'method' => 'patch',
           'route' => ['update_subscription', $user->id]
          ]) !!}
          <div>
            <input type="checkbox" class="cb-box" name="subscribe" value='{{$user->subscribe}}' {{ ( $user->subscribe ) ? 'checked=checked': ''}}/> <h4>Subscribe to latest software/firmware updates</h4>
          </div>
          <br>
          <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-check"></i> <span>subscribe</span></button>
      {!! Form::close() !!}



    @endcomponent
@endsection


@section('css')
<style>
  form {
    margin-top: 30px;
  }
  .cb-box {
    float: left;
    margin-right: 10px !important;
  }
  h4 {
    margin-left: 5px;
  }
  .contact-list {
    max-width: 100%;
  }
  .contact-list .grid > div {
    padding: 10px 5px;
  }
</style>
    
@stop
