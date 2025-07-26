@extends('layouts.app')

@section('content')
  <div class="panel panel-top">
    {!! Breadcrumbs::render('addCertificate') !!}
  </div>
  @include('components/flash')
  <div class="row">
    <div class="col-sm-6">
        @component('components/panel')
            @slot('title')
                 Upload Certificate
            @endslot
            {!! Form::open(['route' => 'certificates.store', 'files' => true,'id' => 'fileupload' ]) !!}
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <div class="form-group">
                  <label for="InputFile">File Name</label>
                  <input type="text" id="InputFilename" name="filename" />
               </div>
               <div class="form-group">
                  <label for="InputFile">Upload File</label>
                  <input type="file" id="InputFile" name="files" />
               </div>

              <button  type='button' class="btn-brand-icon btn-brand-primary btn-brand" id="btnLoad" onclick="showFileSize();"><i class="fa fa-upload"></i><span>Load File</span></button>
              <br><br>
            {{ Form::close() }}
        @endcomponent
    </div>
    <div class="col-sm-6">
        @component('components/panel')
            @slot('title')
               Uploader
            @endslot
            <div class="form">
              <div class="file-list">
                <table class="table table-brand-default" id="table-listing">
                  <tr>
                    <th>Filename</th>
                    <th>Filesize</th>
                    <th width="200">Action</th>
                  </tr>
                </table>
              </div>
          </div><!-- ./ End of Form -->
          <div class="hide loading"><div><img src="{{ asset('public/images/loading.gif') }}" /></div></div>
        @endcomponent
    </div>
  </div>
@endsection


@section('css')
<style>
  .loading {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      color: #fff;
      z-index: 9999;
      background: rgba(251, 250, 250, 0.8);
  }
  .loading > div {
      position: absolute;
      top: 50%;
      left: 50%;
      max-width: 250px;
      padding: 50px;
      transform: translate(-50%, -50%);
  }
  .loading > div img {
      width: 100%;
  }
  .fileinput-button input {
      position: absolute;
      top: 0;
      right: 0;
      margin: 0;
      opacity: 0;
      -ms-filter: 'alpha(opacity=0)';
      font-size: 200px !important;
      direction: ltr;
      cursor: pointer;
  }
  #fileupload {
    margin-bottom: 10px;
  }
  #carbonads {
    box-sizing: border-box;
    max-width: 300px;
    min-height: 130px;
    padding: 15px 15px 15px 160px;
    margin: 0;
    border-radius: 4px;
    font-size: 13px;
    line-height: 1.4;
    background-color: rgba(0, 0, 0, 0.05);
  }
  #carbonads .carbon-img {
    float: left;
    margin-left: -145px;
  }
  #carbonads .carbon-poweredby {
    display: block;
    color: #777 !important;
  } 
   #InputFilename {
    margin-bottom: 10px;
   }
</style>

@stop

@section('js')

<script>
  function showFileSize() {
    var input, file;

    // (Can't use `typeof FileReader === "function"` because apparently
    // it comes back as "object" on some browsers. So just see if it's there
    // at all.)

      input    = document.getElementById('InputFile');
      filename = $('#InputFilename').val();
      table    = document.getElementById('table-listing');


      file = input.files[0];


      if(filename === '') {
        $('#InputFilename').addClass('has-error');
        return false;
      } else {
        $('#InputFilename').removeClass('has-error');
      }

      if(typeof file === 'undefined') {
        $('#InputFile').addClass('has-error');
        return false;
      } else {
        $('#InputFile').removeClass('has-error');
      }

        $('#btnLoad').attr('disabled', true);

       $(table).append('<tr><td><div><input type="hidden" class="file-info" value="'+ filename +'" /> '+ filename +'</div></td><td>'+ humanFileSize(file.size)+'</span></td><td class="btn-action"><button class="submit-file btn-brand-icon btn-brand-success btn-brand"><i class="fa fa-upload"></i><span>Upload</span></button></td></tr>');




       $(table).on('click','.submit-file', function(e){

            e.preventDefault();

            $('.loading').removeClass('hide');

        var file_data = $('#InputFile').prop('files')[0],
            submitBtn = $(this),
            form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('filename', filename);

              $.ajax({
                  url: '{{ url('/certificates/store') }}',
                  type: 'POST',              
                  dataType    : 'text',         
                  cache       : false,
                  contentType : false,
                  processData : false,
                  data        : form_data, 

                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                      //'Content-type': 'text/plain'
                  },
                  success: function(result)
                  {

                     if(result == 'yes') {
                        location.reload();
                        submitBtn.prop('disabled', true);
                        submitBtn.after('<i class="fa fa-check"></i> <span>Done</span>');
                        submitBtn.addClass('hide');
                        $('.loading').addClass('hide');

                        $('#InputFilename').val('');
                        $('#InputFile').val('');
                     }  

                  },
                  error: function(data)
                  {
                      console.log(data);
                  }
              });
       });
}
function humanFileSize(size) {
    var i = Math.floor( Math.log(size) / Math.log(1024) );
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

</script>
@stop