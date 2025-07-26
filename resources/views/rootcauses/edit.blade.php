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
               
               @if($rootcause)


                {!! Form::model($rootcause, [
                                   'method' => 'patch',
                                   'route' => ['rootcause.update', $rootcause->id],

                     ]) !!}

                      <div class="form-group">
                        <label for="exampleInputEmail1">Name</label>
                        <input type="text" class="form-control" name="name"  value="{{ $rootcause->name }}" required/>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Description</label>
                        <textarea type="text" name="description" rows="10">{{ $rootcause->description }}</textarea>
                      </div>

                  
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-check btn-check"></i><span>Update</span></button>
                  {!! Form::close() !!}

                @endif 
               </div> 
           </div>
        </div>
    </div>

@endsection
