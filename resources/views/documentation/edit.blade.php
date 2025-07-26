@extends('layouts.app')

@section('content')
<div class="panel panel-top">
  {!! Breadcrumbs::render('editDocs') !!}
</div>
<div class="row">
   <div class="col-lg-12 form-uploader"> 

      <div class="panel panel-default">
        <div class="panel-body">
           @include('components/errors')
  
          
                <h2>Edit Page </h2>
                {!! Form::open(['route' => array('technical-documentation.update', $file->id), 'method' => 'patch', 'autocomplete' => 'off' ]) !!}

                
                    <div class="form-group">
                        <label for="selectCat">File name</label>

                        <input type="text" name="name" value="{{ $file->filename }}" class="form-control" style="max-width: 320px;"/><br>
                        <input type="text" name="version" value="{{ $file->version }}" class="form-control" style="max-width: 320px;"/>
                        <input type="hidden" name="id" value="{{ $file->id }}" class="form-control" readonly="readonly" />
                    </div>
                
                    <button type='submit' class="btn-brand btn-brand-icon btn-brand-primary" id='btnLoad'><i class="fa fa-check"></i><span>Save Changes</span></button>

                {{ Form::close() }}
          
            </div>
          </div>    
    </div>

</div>

@endsection

