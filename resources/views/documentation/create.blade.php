@extends('layouts.app')

@section('content')
<div class="panel panel-top">
  {!! Breadcrumbs::render('addDocs') !!}
</div>
@include('components/flash')
<div class="row">
   <div class="col-lg-6 form-uploader"> 

      <div class="panel panel-default upload-tab">
        <div class="panel-body">
           @include('components/errors')
  
             <div class="form">
                <h2>Upload Technical Documentation</h2>
                {!! Form::open([
                      'route' => 'firmwares.store',
                      'files' => true,
                      'id' => 'fileupload' 
                    ]) !!}

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="InputFile">File Name</label>
                        <input type="text" name="name" class="form-control" id="docName" required/>
                    </div> 
                    <div class="form-group">
                        <label for="selectCat">Name</label>
                        @if($categories)
                          <select class="form-control" id="firmware" name="category">
                                <option value="">Select</option>

                                @foreach($categories as $category)
                                   <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                                  <option value="new" id="createCategory">Create new</option>
                          </select>
                        @endif
                        <input type="text" name="new_category" id="newCategory" class="form-control {{ ( !empty($categories) ) ? 'hide':'' }}"/>
                    </div>

                    <div class="form-group">
                        <label for="selectCat">Releases</label>
                         @if( $releases )
                          <select class="form-control" id="firmware_releases" name="category">
                              <option value="">Select</option>
                             @foreach( $releases as $release )
                              <option value="{{ $release->id }}">{{ $release->name }}</option>
                              @endforeach
                              <option value="new" id="createRelease">Create new</option>
                          </select>
                         @endif
                         
                        <input type="text" name="new_release" class="form-control {{ ( !empty($categories) ) ? 'hide':'' }}" id="newRelease"/>
                    </div>

                    <div class="form-group">
                        <label for="InputFile">Version</label>
                        <input type="text" name="version" class="form-control" id="firmwareVersion"/>
                    </div>  

                     <div class="form-group">
                        <label for="InputFile">Upload File</label>
                        <div class="file-upload-wrapper">
                           <span><span class="text">Upload</span><input type="file" id="InputFile" name="files" class="form-control" onChange="showFileInfo()" /></span>
                           <div class="hide file-info"></div>
                        </div>
                    </div>

                    {{-- <div class="form-group">
                        <label for="documentInput">Upload Documentation</label>
                        <div class="file-upload-wrapper">
                            <span><span class="text">Upload</span><input type="file" id="documentInput" class="form-control" name="document" onChange="showDocInfo()" /></span>
                            <div class="hide doc-info"></div>
                        </div>
                    </div> --}}

                    <button type='submit' class="btn-brand btn-brand-icon btn-brand-secondary" id='btnLoad'><i class="fa fa-check"></i><span>Add Files</span></button>

                {{ Form::close() }}
              </div><!-- ./ End of Form -->
            </div>
          </div>    
    </div>
    <div class="col-lg-12 load-tab">

      <div class="panel panel-default upload-tab">
        <div class="panel-body">
            <div class="file-list">
                <div class="wrap-table">
                  <table class="table" id="table-listing">
                  </table>
                </div>
            </div> 
        </div>
      </div>      

    </div>  
</div>

@endsection


@section('css')
<style>
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
  /**
  * Table Area
  */
  #table-listing tr:nth-child(odd) {
      background: #f9f8f8;
  }
  #table-listing tr td {
      padding: 20px;
  }
   #table-listing  tr td .btn-danger {
      background-color: #f15d22;
  }
  .divider {
      position: relative;
      width: 100%;
      display: block;
      margin: 10px 0;
      z-index: 3;
  }
  .divider span {
      position: relative;
      z-index: 3;
      background: #fff;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: 600;
      color: #b1b0b0;

  }
  .divider:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 1px;
      border: 1px dashed #f3f1f1;
      top: 50%;
      left: 0;
      z-index: 1;
  }
  #fileupload input[type="text"],
  #fileupload select {
    background: #f5f7fa;
    border: 1px solid #e6e9ef;
    border-radius: 3px;
    height: 40px;
    margin-bottom: 10px;
    webkit-box-shadow: inset 0 1px 1px rgba(173, 170, 170, 0.075);
    box-shadow: inset 0 1px 1px rgba(173, 170, 170, 0.075);
  }
  #fileupload input[type=file] {
    display: block;
  }
  .upload-tab .form .form-group {
     padding: 0;
     border: 0;
     margin-bottom: 30px;
     background: transparent;
  }
  .form h2 {
      font-family: 'Roboto', sans-serif;
      color: #093c69;
      text-align: center;
      margin: 10px auto 30px;
      font-size: 22px;
      padding-bottom: 10px;
      border-bottom: 2px solid #2680d0;
  }
  .file-list ol {
      padding-left: 15px;
      margin-bottom: 30px;
  }
  .file-list ol li {
      margin-bottom: 10px;
  }
  #table-listing th {
    background: #222;
    color: #fff;
  }
  #table-listing th:first-child {
    width: 25% !important;
  }
  .wrap-table {
    padding: 10px;
    border: 1px solid #ddd;
    min-height: 200px;
  }
  .input-error {
      border: 1px solid #ef7777 !important;
  }
  .file-upload-wrapper {
    position: relative;
    padding: 10px;
    border: 2px dashed #f3f0f0;
  }
  .file-upload-wrapper input {
    opacity: 0;
  }
  .file-upload-wrapper input:hover {
     cursor: pointer;
  }
  .file-upload-wrapper > span {
      display: block;
      position: relative;
      background: #1d85d2;
      border-radius: 3px;
  }
  .file-upload-wrapper > span:hover {
     cursor: pointer;
  }
  .file-upload-wrapper span > .text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-transform: uppercase;
    font-weight: 600;
    font-size: 14px;
    color: #fff;
  }
  .file-info,
  .doc-info {
      margin: 5px 0;
      text-align: center;
      font-weight: 600;
      color: #505050;
      word-break: break-all;
  }
  .help-block {
    text-align: center;
  }
  .progress {
    display: none;
    text-align: center;
    width: 100%;
    height: 10px;
    background: #dce0da;
    transition: width .3s;
  }
  .progress.hide {
      opacity: 0;
      transition: opacity 1.3s;
  }
  .progress-bar {
    min-with: 200px;
    min-height: 10px;
  }
  .submit-file.waiting {
    background: transparent;
    padding-left: 0;
  }
  input#btnLoad {
      padding: 5px 10px;
      font-size: 16px;
      background: #31d813;
  }
   .load-tab {
   display: none;
  }
  #table-listing tr td .btn-danger {
    background-color: #f15d22;
    text-align: center;
    padding: 5px 10px;
    display: flex;
    justify-content: center;
    color: #fff !important;
  }
  .wrap-table {
      padding: 10px;
      border: 0;
      min-height: auto;
  }
  .upload-tab {
      padding-top: 0;
      min-height: auto;
      border: 0;
      border-radius: 3px;
  }
  .table {
    margin-bottom: 0;
  }
  .submit-file {
    float: right;
  }
</style>
@stop

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

<script>
  /**
   * Showing new input
   * 
   */
  $('#firmware').on('change', function(){

      if( $('#firmware option:selected').val() == 'new') {
        $('#newCategory').removeClass('hide');
      } else {
        $('#newCategory').addClass('hide');
      }
     
  });

  /**
   * Showing new input
   * 
   */
  $('#firmware_releases').on('change', function(){

      if( $('#firmware_releases option:selected').val() == 'new') {
        $('#newRelease').removeClass('hide');
      } else {
        $('#newRelease').addClass('hide');
      }
     
  })

  /**
   * Showing Document Information
   * 
   */
  function showDocInfo() {
          var doc          = document.getElementById('documentInput');
          var fileinfo     = document.querySelector('.doc-info');
          var docFilename = doc.files[0]['name'];
          var docFilesize = doc.files[0]['size'];

          docFilesize = bytesToSize(docFilesize );

          fileinfo.classList.remove('hide');
          fileinfo.innerHTML = "<span>" + docFilename + "</span><br><span>" + docFilesize + "</span>";
  }


  /**
   * Showing File Information
   * 
   */
  function showFileInfo() {
          var inputFile    = document.getElementById('InputFile');
          var filename = inputFile.files[0]['name'];
          var filesize = inputFile.files[0]['size'];

          filesize = bytesToSize(filesize);

          var fileinfo = document.querySelector('.file-info');

 
          fileinfo.classList.remove('hide');
          fileinfo.innerHTML = "<span>" + filename + "</span><br><span>" + filesize + "</span>";
  }

   /**
   * Determining size of the file
   * 
   */
  function bytesToSize(bytes) {
          var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
          if (bytes == 0) return 'n/a';

          var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
          if (i == 0) return bytes + ' ' + sizes[i]; 
          return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
  }

    /**
   * Checking all files and adding it to the table
   * 
   */
  $('#btnLoad').on('click', function(e){

      e.preventDefault();

      var input, file;

      // (Can't use `typeof FileReader === "function"` because apparently
      // it comes back as "object" on some browsers. So just see if it's there
      // at all.)

      var 
      docName      = $('#docName'),
      category     = $('#firmware'),
      release      = $('#firmware_releases'),
      release_select = $('#firmware_releases option:selected').val(),
      cat_selected = $('#firmware option:selected').val(),
      new_category = $('#newCategory').val(),
      error_count  = 0,
      new_release  = $('#newRelease').val(),
      inputFile    = document.getElementById('InputFile'),
      //doc          = document.getElementById('documentInput'),
      table        = document.getElementById('table-listing'),
      version      = $('#firmwareVersion');


      //docu = doc.files[0];
      file = inputFile.files[0];


      // Validate Firmware Input
      if( docName.val() == '') {
          docName.addClass('input-error');
          error_count++;
      }  else if( docName.val() == 'new' ) {
          $('#docName').addClass('input-error');
          error_count++;
      } else {
          $('#docName').removeClass('input-error');
          if( docName.hasClass('input-error') ) {
             docName.removeClass('input-error');
             error_count--;
          } 
      }

      // Validate Firmware Input
      if( category.val() == '' && new_category == '') {
          category.addClass('input-error');
          error_count++;
      }  else if( category.val() == 'new' && new_category == '' ) {
          $('#newCategory').addClass('input-error');
          error_count++;
      } else {
          $('#newCategory').removeClass('input-error');
          if( category.hasClass('input-error') ) {
             category.removeClass('input-error');
             error_count--;
          } 
      }

      // Validate Release
      if( release.val() == '' && new_release == '') {
          release.addClass('input-error');
          error_count++;
      } else if( release.val() == 'new' && new_release == '') {
          $('#newRelease').addClass('input-error');
          error_count++;
      } else {
          $('#newRelease').removeClass('input-error');
          if( release.hasClass('input-error') ) {
             release.removeClass('input-error');
             error_count--;
          } 
      }


      // Validate Version
      if( version.val() == '') {
          version.addClass('input-error');
          error_count++;
      } else {
          if( version.hasClass('input-error') ) {
             version.removeClass('input-error');
             error_count--;
          } 
      }

      var file_parentElement = $('#InputFile').closest('.file-upload-wrapper');

      // Validate File Upload
      if( inputFile.value == '' ) {
          //file_parentElement.classList.add('input-error');
          file_parentElement.addClass('input-error');
          error_count++;
      } else {
          if( file_parentElement.hasClass('input-error') ) {
             file_parentElement.removeClass('input-error');
             error_count--;
          } else {
            file_parentElement.removeClass('input-error');
          }
      }


      if( error_count > 0 ) {
        return false;
      } 

      $('.load-tab').show();
      $('.form-uploader').addClass('hide');
      //display table when no errors
      cat = new_category ? $('#newCategory').val() : $('#firmware option:selected').text(); 
      rel = new_release ? $('#newRelease').val() : $('#firmware_releases option:selected').text();

      //If load - show table list
      display_table(table,  file, rel, cat, version.val());


});

 /**
  *  Upload file information
  * 
  */
 $('#table-listing').on( 'click', '.submit-file', async function(e) {

      e.preventDefault();

      var inputFile    = document.getElementById('InputFile');
      var filename     = inputFile.files[0]['name'],
      docName          = $('#docName').val(),
      filesize         = inputFile.files[0]['size'],
      category         = $('#firmware'),
      release          = $('#firmware_releases'),
      release_select   = $('#firmware_releases option:selected').val(),
      cat_selected     = $('#firmware option:selected').val(),
      new_category     = $('#newCategory').val(),
      error_count      = 0,
      new_release      = $('#newRelease').val(),
      inputFile        = document.getElementById('InputFile'),
      table            = document.getElementById('table-listing'),
      version          = $('#firmwareVersion');


      var file_data = inputFile.files[0]
          submitBtn = $(this),
          version   = $('#firmwareVersion').val(),
          form_data = new FormData();

          form_data.append('filename', docName);
          form_data.append('file', file_data);
          form_data.append('category', cat_selected);
          form_data.append('new_category', new_category);
          form_data.append('new_release', new_release);
          form_data.append('release_select', release_select);
          form_data.append('version', version);


          $(this).text('Please wait!');
           $(this).closest('button').removeClass('btn-primary').addClass('btn-danger').addClass('waiting');
          $(this).closest('button').attr('disabled', true);

          const result = await Swal.fire({
            title: "New Updates",
            text: "Would you like to send notification about this document to your subscribers?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            cancelButtonText: "No"
          });

          if (result.isConfirmed) {
            form_data.append('notif_subscriber', 1);
          }

          $.ajax({
             xhr: function () {
                  var xhr = new window.XMLHttpRequest();
                  xhr.upload.addEventListener("progress", function (evt) {
                      if (evt.lengthComputable) {
                          var percentComplete = evt.loaded / evt.total;
                          //console.log('progress : ' + percentComplete);
                          submitBtn.closest('td').find('.progress').show();
                          submitBtn.closest('td').find('.progress-bar').css({
                              width: percentComplete * 100 + '%'
                          });
                          //console.log('percent : ' + ( Math.round(percentComplete * 100 ) ) + '%');
                          //submitBtn.closest('td').find('.percentage-uploaded').html('Uploaded : <span>'+ ( Math.round(percentComplete * 100 ) ) + '%</span>');
                          if (percentComplete === 1) {
                              //$('.progress').addClass('hide');
                          }
                      }
                  }, false);

                  return xhr;
              },
              url: '{{ url('/technical-documentation/store') }}',
              type: 'POST',              
              dataType    : 'text',           // what to expect back from the PHP script, if anything
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
                 //If uploaded from the server
                 if(result == 'yes') {

                     setInterval(function(){ location.href = '{{ url('technical-documentation') }}' }, 1500); 

                     submitBtn.prop('disabled', true);
                     //submitBtn.text('Uploaded').addClass('hide');
                     //submitBtn.after('New File has been added successfully!  ');

                 }  

              },
              error: function(data)
              {
                  console.log(data);

                  submitBtn.text(data['statusText']);
              }
          });
 });





  /**
   * Display Table after adding files
   */
 function display_table(table, file,  release_select, name, version ) {

        $output = '';

            $output += '<tr>';
              $output += '<td>Name : ' + name + '<br> Release : '+ release_select + '<br> Version : ' + version + '</td>';
              $output += '<td width="30%"><div><input type="hidden" class="file-info" value="'+ file +'" /><span>Filename</span> : '+ file.name +'</div><span>Filesize</span> : '+ bytesToSize(file.size) +'</span>';
              $output += '</td>';
              $output += '<td>';
                $output += '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>';
                $output += '<button class="submit-file btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check"></i><span>Upload File</span></button><strong class="percentage-uploaded"></strong>';
              $output += '</td>';  
            $output += '</tr>';


    return $(table).append($output);
   }

  /**
   * Removing file after successful upload
   */
  $('#table-listing').on('click', '.remove-file', function(){
      location.reload();
      $(this).closest('tr').remove();
  })


</script>
@stop