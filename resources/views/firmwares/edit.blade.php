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
  
          
                {!! Form::open(['route' => array('firmwares.update', $file->id), 'method' => 'patch', 'autocomplete' => 'off' ]) !!}

                    <div class="form-group">
                       <input type="checkbox" name="latest" id="latestFirmware" <?php echo ( $file->latest) ? 'checked': '' ?>/> <strong class="latest-label"> Mark as latest software/firmware file</strong>
                    </div>
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

@section('css')
<style>
  #latestFirmware {
      float: left;
      margin-right: 10px;
  }
  .latest-label {
      color: #004181;
      font-size: 16px;
  }
</style>
@stop