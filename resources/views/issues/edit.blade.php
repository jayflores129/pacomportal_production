@extends('layouts.app')

@section('content')
    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('addIssues') !!} 
          </div>
          <div class="col-sm-6 text-right">
           
          </div>
        </div>
      </div> 
    @endif
    <div class="panel panel-default panel-brand">
        <div class="panel-heading">
          <h3>New Issue</h3>
        </div>
        <div class="panel-body">
           <div class="row">
              <div class="col-md-6">

                   <div class="form">
                       @include('components/errors')
                    
                       {!! Form::model( $issue, [
                                  'method' => 'patch', 
                                  'route' => ['issues.update', $issue->id]
                        ]) !!}
                            <div class="form-group">
                              <label for="exampleInputEmail1">Add Issue ( read-only )</label>
                              <input type="text" class="form-control" name="name" value="{{ $issue->name }}" required readonly="readonly" />
                            </div>
                            <div class="form-group">
                              <label for="exampleInputEmail1">Description</label>
                              <textarea type="text" name="description" rows="10">{{ $issue->description }}</textarea>
                            </div>

                            <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
                        {!! Form::close() !!}
                   </div>

              </div>
           </div>
        </div>
    </div>

@endsection
