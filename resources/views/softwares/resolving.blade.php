@extends('layouts.app')

@section('content')

  @include('softwares/show/panel') 
  @include('components/flash') 

  @include('softwares/show/resolution') 

  @include('softwares/show/info')

       
@endsection

@section('css')
<style>
  a.btn-brand.btn-brand-padding.downloadable-file {
    background: #3097d1;
    border-radius: 4px;
  }
  .attachment-uploader,
  .comment-form {
    background: #f5f5f5;
    border: 1px dashed #ddd;
    padding: 20px;
    margin-top: 20px;
  }
  .m-height {
    margin: 10px 0;
    min-height: 70px;
  }
  .l-height {
    margin: 10px 0;
    min-height: 100px;
  }
  #taskInfo .grid .col:first-child {
     width: calc(100% - 300px);
  }
  .single-profile.clearfix {
    padding: 10px 0;
  }
  .single-profile .photo {
    max-width: 150px;
  }
  #taskInfo .grid .col:last-child {
      width: 300px;
      padding: 0 25px;
      border-left: 1px solid #fbfafa;
  }
  .dz-default.dz-message {
    background: #f5f5f5;
    padding: 30px;
    border: 2px dashed #ddd;
    margin: 10px 0;
  }
  .dz-default.dz-message span {
    padding: 20px;
    display: block;
    background: #f9f9f9;
    text-align: center;
    font-weight: 600;
  }
  .attachment-uploader,
  .comment-form {
    display: none;
  }
  .task-fix-height {
    max-height: 700px;
    overflow-y: auto;
  }
  .add-comment-form {
    margin-top: 20px;
    padding: 10px;
    border: 1px solid #f5f2f2;
  }
  .attachment-upload-status {
      background: #fbfafa;
      padding: 8px 10px;
      border: 1px solid #efecec;
  }
  .percentage-uploaded {
    display: inline-block;
    padding: 4px 10px;
    background: #bcf5be;
    border-radius: 3px;
    color: #2c842f;
    font-weight: 600;
    font-size: 20px;
  }
</style>
@stop
@section('js')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script>
   $('#resolvetask').on('click', function(){
      
    $(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');

    });
    $('#attachmentFileSubmit').on('click', function(e){
        
      e.preventDefault();

      var name        = $('#attachmentName').val();
      var description = $('#attachmentDescription').val();
      var task_id     = $('#attachmentTaskID').val();
      var assigned_to = $('#attachmentAssign').val();
      var created_by  = $('#attachmentCreated').val();
      var attachment  = document.getElementById('attachmentFile').files[0];


      if( name == '') {
         $('#attachmentName').addClass('has-error');
         return false;
      } else {
        $('#attachmentName').removeClass('has-error');
      }

      
     if( $('#attachmentFile').val() == '') {
         $('#attachmentFile').addClass('has-error');
         return false;
      } else {
        $('#attachmentFile').removeClass('has-error');
      }
      console.log( attachment);
      $('.attachment-uploader').toggleClass('show');
      $('.attachment-upload-status').toggleClass('hide');
      $('.new-revision').remove();
      $('.attachment-upload-status .progress').removeClass('hide');

      form_data = new FormData();
      form_data.append('version', name);
      form_data.append('description', description);
      form_data.append('file', attachment);
      form_data.append('task_id', task_id);
      form_data.append('created_by', created_by);
      form_data.append('assigned_to', assigned_to);

      $.ajax({
         xhr: function () {
              var xhr = new window.XMLHttpRequest();
              xhr.upload.addEventListener("progress", function (evt) {
                  if (evt.lengthComputable) {
                      var percentComplete = evt.loaded / evt.total;
           
                      $('.attachment-upload-status .progress-bar').css({
                          width: percentComplete * 100 + '%'
                      });
 
                  }
                  if( ( Math.round(percentComplete * 100 ) ) == 100 ) {
                     $('.attachment-upload-status .percentage-uploaded').append('<span class="uploading-status">Please wait...</span>');
                  }
              }, false);
              
              return xhr;
          },
          url: '{{ url('/admin/software-upload-attachment') }}',
          type        : 'POST',              
          dataType    : 'text',       
          cache       : false,
          contentType : false,
          processData : false,
          data        : form_data, 
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
              //'Content-type': 'text/plain'
          },
          success: function(data)
          {
              console.log(data);
               //If uploaded from the server
               if(data) {
                  
                  location.reload();
                   
                  $('.attachemnt-uploader input[name="version"]').val('');
                  $('.attachemnt-uploader input[name="description"]').val('');
                  $('.attachemnt-uploader input[type="file"]').val('');
            

               } else {
                     
                     console.log('upload not success');
          
                }

          },
          error: function(data)
          {
              console.log(data);
          }
      });


    });

    $('.comment-form button[type="submit"]').on('click', function(){
        
      $(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');

    });
    $('.add-comment-form button[type="submit"]').on('click', function(){
        
      $(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');

    });
    function goBack() {
        window.history.back();
    }

    $('.new-revision').on('click', function(e) {

        e.preventDefault();

        $('.attachment-uploader').toggleClass('show');

    });
    $('.new-comment').on('click', function(e) {

        e.preventDefault();

        $('.comment-form').toggleClass('show');

    });

    $('.dropdown-comments button').on('click', function(e){

      e.preventDefault();


      var task_id = $(this).closest('.attachment__revision').attr('id');

      $('.attachment-listing').find('li').removeClass('is-active');
      $('.attachment-listing button').removeClass('is-viewing');
      $('.attachment-listing button').find('span').text('comments');

      $('.attachment-wrapper').removeClass('is-wide');
      $('.comment-wrapper').removeClass('is-hide');


      $(this).closest('button').addClass('is-viewing');  
      $(this).closest('li').addClass('is-active');


      if( $(this).closest('button').hasClass('is-viewing') ) {

        $(this).find('span').text('comments');

      } else {
         $(this).find('span').text('comments');
      }
       
      $('.add-comment-form').find('#formAttachmentID').val(task_id);

      $.ajax({
          type : 'GET',
          url  : '{{URL::to('admin/get-comments') }}',
          data: {
                  'id' : task_id
          },
           success: function(data) {

              var output = '';

              if(data['length'] > 0 ) {
      
                 for( var z = 0; z < data['length']; z++) {

                    output += '<div class="single-comment">';
                       output += '<img src="'+ data['data'][z]['photo'] +'" width="24"/>';
                      output += '<span><strong>'+ data['data'][z]['created_by'] +'</strong> - '+ data['data'][z]['created_at']  +'</span>';

                      output += '<p>'+ data['data'][z]['description'] +'</p>';
                    output += '</div>';
                 }

              } else {
                  output = '<div class="single-comment"><p>No comment</p></div>';
              }

              $('.comment-wrapper').find('.scrollable').html(output);
           },
           error: function(error) {
              console.log(errror);
           }

       })


    });


    $('#closeCommentHolder').on('click', function(e){
      e.preventDefault();

      $('.attachment-wrapper').addClass('is-wide');
      $('.comment-wrapper').addClass('is-hide');
    });

    $('.downloadable-file').on('click', function(){

        var filename = $(this).attr('href');

       console.log(filename);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'post',
          url: '{{ URL::to('task-file-download') }}',
          data   : {
            'downloaded' : true,
            'filename' : filename
          },
          success : function(data){
              console.log(data);
          },
          error : function(err) {
            console.log(err);
          }  

        });

    });
</script>
@stop

