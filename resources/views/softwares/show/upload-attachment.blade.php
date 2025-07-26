@extends('layouts.app')

@section('content')

  @include('softwares/show/panel') 
  @include('components/flash') 


  <div class="single-task-content tab-content">

    <div id="taskAttachment" class="tab-pane fade in active">
      test


    </div>
  </div>       

   
          
@endsection

@section('css')
<style>
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
</style>
@stop
@section('js')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script>

    function goBack() {
        window.history.back();
    }


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

