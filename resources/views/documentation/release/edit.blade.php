@extends('layouts.app')

@section('content')
<div class="panel panel-top">
  {!! Breadcrumbs::render('updateFirmwares') !!}
</div>
<div class="row">
   <div class="col-lg-12 form-uploader"> 

      <div class="panel panel-default">
        <div class="panel-body">
           @include('components/errors')
  
  
                <h2>Edit release</h2>
                {!! Form::open(['route' => array('technical-documentation.updateRelease', $release->id), 'method' => 'patch', 'autocomplete' => 'off' ]) !!}

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                    <div class="form-group">
                        <label for="selectCat">Name</label>

                        <input type="text" name="name" value="{{ $release->name }}" class="form-control" style="max-width: 320px;"/>

                    </div>
                
                    <button type='submit' class="btn-brand btn-brand-icon btn-brand-primary" id='btnLoad'><i class="fa fa-check"></i><span>Save Changes</span></button>

                {{ Form::close() }}
          
            </div>
          </div>    
    </div>

</div>

@endsection

