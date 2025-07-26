@extends('layouts.app')

@section('content')
 
  <div class="panel panel-top">
    <div class="row">
      <div class="col-sm-6">
        {!! Breadcrumbs::render('addSoftware') !!} 
      </div>
      <div class="col-sm-6 text-right">
        
      </div>
    </div>
  </div> 
  @include('components/flash')
  <div class="row">
    <div class="col-sm-6 col-left">
      
        @component('components/panel')
          @slot('title')
             New Task
          @endslot

          @include('components/errors')

             {!! Form::open(['route' => 'softwares.store','autocomplete' => 'off', 'files' => true]) !!}

                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                  <label for="taskType">Choose Type <sup>*</sup></label>
                  <select  id="taskType" class="required-field" name="type" required>
                      <option value="">Select</option>
                      <option value="Defect">Defect</option>
                      <option value="Feature">Feature</option>
                      <option value="Task">Task</option>
                  </select>
                </div>
                @if ($products)
                  <div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
                    <label for="taskProduct">Choose a Product <sup>*</sup></label>
                    <select  id="taskProduct" name="product" class="required-field" required>
                        <option value="">Select</option>

                        @foreach ($products as $product)
                          <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach  

                    </select>
                  </div>
               @endif
                <div class="form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                  <label for="taskSummary">Summary <sup>*</sup></label>
                  <input type="text" id="taskSummary" class="form-control required-field" name="summary"  required/>
                </div>
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  <label for="taskDescription">Description</label>
                  <textarea class="form-control required-field" name="description"  id="taskDescription" rows="3"></textarea>
                </div>
                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                  <label for="taskAttachment">Attachment</label>
                  <input type="file" id="taskAttachment" class="form-control" name="file"  />
                </div>
                <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                  <label for="taskStatus">Status <sup>*</sup></label>
                  <select  type="text" id="taskStatus" class="form-control required-field" name="status" required>
                      <option value="">Select</option>
                      <option value="To Do">To Do</option>
                      <option value="In Progress">In Progress</option>
                      <option value="Completed">Completed</option>
                  </select>
                </div>
                <div class="form-group{{ $errors->has('problem_description') ? ' has-error' : '' }}">
                  
                  @php $default_assignee    = App\Models\Option::where('key','default_assignee_for_new_task' )->value('value'); @endphp
                  
                  @if( $default_assignee && Auth::user()->hasRole('customer') )
                    <p>This task will be assigned to SPG Support automatically</p> 
                    <input type="hidden" id="assignTo" class="form-control" name="assignee" value="{{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'SPG Support':'' }}" required {{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'readonly="readonly"':'' }}/>
                   <input type="hidden" id="assigneeID" class="form-control" name="assigneeID" value="{{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? $default_assignee:'' }}" {{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'readonly="readonly"':'' }}/>
                  @else
                    <label for="assignTo">Assignee <sup>*</sup></label>
                    <input type="text" id="assignTo" class="form-control" name="assignee" placeholder="Begin type a user name here..." value="{{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'SPG Support':'' }}" required {{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'readonly="readonly"':'' }}/>
                  <input type="hidden" id="assigneeID" class="form-control" name="assigneeID" value="{{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? $default_assignee:'' }}" {{ ( $default_assignee && Auth::user()->hasRole('customer') ) ? 'readonly="readonly"':'' }}/>
                  @endif

                  
                  <div id="userList" ></div>
                </div>


                <button  class="btn-brand btn-brand-icon btn-brand-primary" id="addTask"><i class="fa fa-check"></i><span>Submit</span></button>
              {!! Form::close() !!}

        @endcomponent

    </div>
    <div class="col-sm-6 col-right">
        <div class="panel panel-default panel-brand">
            <div class="panel-heading">
                <h3>Task</h3>
            </div>
            <div class="panel-body">
              <div class="progress">
                 <div class="progress-bar"></div>
              </div>
              <div class="percentage-uploaded"></div>
            </div>
        </div>    
    </div>
  </div>
                         
      
@endsection

@section('css')
<style>
 .percentage-uploaded {
    display: none;
    padding: 4px 10px;
    background: #bcf5be;
    border-radius: 3px;
    color: #2c842f;
    font-weight: 600;
    font-size: 20px;
  }
  #userList {
      margin: 10px 0;
      max-height: 500px;
      overflow: auto;
  }
  #userList .single-contact {
    padding: 5px 10px;
    border-bottom: 1px solid #f5f3f3;
  }
  .single-contact strong {
    line-height: 35px;
  }
  .single-contact::after {
    content: '';
    display: block;
    clear: both;
  }
  .single-contact .btn-brand-icon span {
    width: 100%;
  }
  .progress {
    display: none;
  }
  .has-error {
    border: 1px solid red !important;
  }
  .uploading-status {
    line-height: 26px;
    font-size: 20px;
    color: #222;
  }
</style>
@endsection


@section('js')
<script>


  $('#assignTo').on('keyup', function(){

    var assignTo = $('#assignTo').val();

     $.ajax({
          type: 'get',
          url: '{{ URL::to('admin/searchUser') }}',
          data: { 'name' : assignTo },
          success: function(data) {
            //console.log(data);
            var total = data.length;
            var output = '';

  
            if(data.length > 0) {
           
              output += '<div class="single-contact" id="{{ $default_assignee }}"><strong>SPG Support</strong><button class="float-right btn-brand btn-brand-icon"><span>Assign</span></button></div>';

              for(var a = 0; a < total; a++) 
              {
                output += '<div class="single-contact" id="'+ data[a]['id']+'"><strong>'+ data[a]['firstname'] + ' ' + data[a]['lastname'] + '</strong><button class="float-right btn-brand btn-brand-icon"><span>Assign</span></button></div>';
              }


             } else {
              output = '<div class="single-contact">No result found!</div>';
             } 

             if( assignTo == '') {
              output = '<div class="single-contact">Start typing to see list of companies</div>';
             }

            $('#userList').html(output);

          },
          error: function(error) {
            //console.log(error);
          }
     })

  });

  $('#userList').on('click', '.btn-brand', function(e){

    e.preventDefault();

    $(this).closest('.single-contact').addClass('selected-contact');
    $(this).html('<span>Assigned</span>').attr('disabled', true);
    $('#assigneeID').val($('.selected-contact').attr('id'));
    $('#assignTo').val($('.selected-contact').find('strong').text());
    $('#userList').find('.single-contact').not('.selected-contact').remove();
    $(this).closest('.single-contact').css('display', 'none');
   
  });

  $('#addTask').on('click', function(e)
  {

          e.preventDefault();
           $('.flash-message').html('');

            var type        = $('#taskType option:selected').val();
            var product     = $('#taskProduct option:selected').val();
            var summary     = $('#taskSummary').val();
            var description = $('#taskDescription').val();
            var attachment  = document.getElementById('taskAttachment');
            var status      = $('#taskStatus option:selected').val();
            var assign      = $('#assignTo').val();
            var assignID    = $('#assigneeID').val();
            var submitBtn   = $(this);

             
            if( type === '' ) {

              $('#taskType').addClass('has-error');
              return false;

            } else {

              if( $('#taskType').hasClass('has-error') ) {

                  $('#taskType').removeClass('has-error');
              }

            } 

            if( product === '' ) {

              $('#taskProduct').addClass('has-error');
              //console.log('prod');
              return false;

            } else {

              if( $('#taskProduct').hasClass('has-error') ) {

                  $('#taskProduct').removeClass('has-error');
              }

            } 

            if( summary === '' ) {

              $('#taskSummary').addClass('has-error');
              return false;

            } else {

              if( $('#taskSummary').hasClass('has-error') ) {

                  $('#taskSummary').removeClass('has-error');
              }

            } 

            if( status === '' ) {

              $('#taskStatus').addClass('has-error');
              return false;

            }  else {

              if( $('#taskStatus').hasClass('has-error') ) {

                  $('#taskStatus').removeClass('has-error');
              }

            } 

            if( assignID === '' ) {

              $('#assignTo').addClass('has-error');
              return false;

            }   else {

              if( $('#assignTo').hasClass('has-error') ) {

                  $('#assignTo').removeClass('has-error');
              }

            } 
          
          $('.col-left').remove();


          form_data = new FormData();
          form_data.append('type', type);
          form_data.append('product', product);
          form_data.append('summary', summary);
          form_data.append('description', description);
          form_data.append('file', attachment.files[0]);
          form_data.append('status', status);
          form_data.append('assign', assign);
          form_data.append('assignID', assignID);

          $(this).attr('disabled', true);
          $('.progress').addClass('show');

          $.ajax({
             xhr: function () {
                  var xhr = new window.XMLHttpRequest();
                  xhr.upload.addEventListener("progress", function (evt) {
                      if (evt.lengthComputable) {
                          var percentComplete = evt.loaded / evt.total;
                          //console.log('progress : ' + percentComplete);
                          $('.progress').show();
                          $('.progress-bar').css({
                              width: percentComplete * 100 + '%'
                          });
                          //console.log('percent : ' + ( Math.round(percentComplete * 100 ) ) + '%');
                          $('.percentage-uploaded').css('display', 'inline-block');
                          //$('.percentage-uploaded').html('Uploading <span>'+ ( Math.round(percentComplete * 100 ) ) + '%</span>');
                          $('.percentage-uploaded').html('<span>Please wait..</span>');
                          if (percentComplete === 1) {
                              //$('.progress').addClass('hide');
                          }
                      }
                      // if( ( Math.round(percentComplete * 100 ) ) == 100 ) {
                      //    $('.percentage-uploaded').after('<br><span class="uploading-status">Reloading the page</span>');
                      // }
                  }, false);
                  
                  return xhr;
              },
              url: '{{ url('/addTask') }}',
              type        : 'POST',              
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
                  //console.log(result);
                 //If uploaded from the server
                 if(result) {
                   location.reload();
                    //$('.uploading-status').html('Thank you for waiting.. <br> New task is added.');
                     
                    //$('.flash-message').html('<p class="alert alert-success">New Software Feature / Defect has been added! <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p');

                    // $('#taskType option:first-child').attr('selected', true);
                    // $('#taskProduct option:first-child').attr('selected', true);
                    // $('#taskStatus option:first-child').attr('selected', true);
                    // $('#taskSummary').val('');
                    // $('#taskDescription').val('');
                    // $('#taskAttachment').val('');
                    // $('#assignTo').val('');
                    // $('#assigneeID').val('');
                    // $('#addTask').attr('disabled', false);

                    // $('.progress').removeClass('show');  
                    // $('.progress-bar').css('width', '0%');

                    //  $(this).attr('readonly', false);

                 }  

              },
              error: function(data)
              {
                  console.log(data);
              }
          });

  });

 

</script>
@endsection
