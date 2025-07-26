@extends('layouts.app')

@section('content')

    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('rootcause') !!} 
          </div>
          <div class="col-sm-6 text-right">
            
          </div>
        </div>
      </div> 
    @endif
    <div class="panel panel-default panel-brand">
        <div class="panel-heading">
          <h3>Edit Root Cause</h3>
        </div>
        <div class="panel-body">
           <div class="form row">
              <div class="col-sm-6">
               @include('components/errors')
               
                @if($rootcause == true)


                      <div class="form-group">
                        <label for="exampleInputEmail1">Name</label><br>
                        {{ $rootcause->name }}
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Description</label><br>
                        {{ $rootcause->description }}
                      </div>
                @else 
                     <h3>Root cause not found!</h3>
                @endif 
               </div> 
           </div>
        </div>
    </div>

@endsection
