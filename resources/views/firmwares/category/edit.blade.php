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
  
          
                <h2>Edit Software/Firmware's name </h2>
                {!! Form::open(['route' => array('firmwares.updateName', $category->id), 'method' => 'patch', 'autocomplete' => 'off' ]) !!}

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                    <div class="form-group">
                        <label for="selectCat">Name</label>

                        <input type="text" name="name" value="{{ $category->name }}" class="form-control" style="max-width: 320px;"/>
                        <input type="hidden" name="id" value="{{ $category->id }}" class="form-control" readonly="readonly" />
                    </div>
                
                    <button type='submit' class="btn-brand btn-brand-icon btn-brand-primary" id='btnLoad'><i class="fa fa-check"></i><span>Save Changes</span></button>

                {{ Form::close() }}
          
            </div>
          </div>    
    </div>

</div>

@endsection

