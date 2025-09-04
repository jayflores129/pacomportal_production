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
          
                <h2>Update</h2>
                <p>
                  Upload a new file of <b>{{ optional($file)->filename }}</b>
                </p>
                <hr>

                <form action="{{ route('firmwares.upload', $file->id) }}" method="POST" enctype="multipart/form-data">
                  @csrf

                  <div class="form-group">
                        <label for="selectCat">Upload File</label>
                        <input type="file" name="file" class="border" style="border:1px solid #eee;padding: 10px;">
                    </div>
                
                    <button type='submit' class="btn-brand btn-brand-icon btn-brand-primary" id='btnLoad'><i class="fa fa-check"></i><span>Submit</span></button>

                </form>
          
            </div>
          </div>    
    </div>

</div>

@endsection

