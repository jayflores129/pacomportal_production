@extends('layouts.app')

@section('content')
    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            <a href="{{ url('admin/softwares/') }}/{{$ticket->id}}" class="btn-brand btn-brand-icon btn-brand-danger" id="addTicket"><i class="fa fa-close"></i><span>Cancel</span></a>
          </div>
          <div class="col-sm-6 text-right">
       
          </div>
        </div>
      </div> 
    @endif

   @include('components/flash')  

  <div class="row">
    <div class="col-sm-7">
      
        @component('components/panel')
          @slot('title')
             Software
          @endslot

            @include('components/errors')

             {!! Form::open( array('method' => 'patch', 'route' =>array('softwares.update', $ticket->id ), 'autocomplete' => 'off' ) ) !!}

                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                  <label for="input_1">Choose Type <sup>*</sup></label>
                  <?php  $types = ['Defect', 'Feature', 'Request']?>
                  <select  id="input_1" name="type" required>
                      @foreach($types as $type)
                          <option value="{{ $type }}" {{ ( $ticket->type == $type ) ?  'selected': '' }}>{{ $type }}</option>
                      @endforeach
                  </select>
                </div>

                @if ($products)
                  <div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
                    <label for="input_pn">Choose a Product <sup>*</sup></label>
                    <select  id="input_pn" name="product" required>
                        <option value="">Select</option>

                        @foreach ($products as $product)
                          <option value="{{ $product->id }}" {{ ( $ticket->product_id == $product->id ) ?  'selected': '' }}>{{ $product->name }}</option>
                        @endforeach  

                    </select>
                  </div>
                @endif

                <div class="form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                  <label for="input_3">Summary <sup>*</sup></label>
                  <input type="text" id="input_3" class="form-control" name="summary" value="{{ $ticket->summary }}"  required/>
                </div>
         
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  <label for="input-pd">Description</label>
                  <textarea class="form-control" name="description"  id="input-pd" rows="3">{{ $ticket->description }}</textarea>
                </div>

                <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                  <label for="input_pn">Status <sup>*</sup></label>
                   <?php  $status = ['To Do', 'In Progress', 'Completed']?>
                  <select  id="input_pn" name="status" required>
                      @foreach($status as $stat) 
                          <option {{ ( $ticket->status == $stat ) ?  'selected': '' }}>{{ $stat }}</option>
                      @endforeach
                  </select>
                </div>

                <div class="form-group{{ $errors->has('problem_description') ? ' has-error' : '' }}">
                  <label for="assignTo">Assign to <sup>*</sup></label>
                  <input type="text" id="assignTo" class="form-control" name="assignee" value="{{ $ticket->assign->firstname . ' '. $ticket->assign->lastname  }}" required/>
                  <input type="hidden" id="assigneeID" class="form-control" name="assigneeID" value="{{ $ticket->assign->id }}"/>
                  <div id="userList"></div>
                </div>

                <button  type="submit" class="btn-brand btn-brand-icon btn-brand-primary" id="addTicket"><i class="fa fa-check"></i><span>Submit</span></button>

                {!! Form::close() !!}
        @endcomponent
    
    </div>
  </div>
                         
      
@endsection

@section('css')
<style>
  #userList {
      margin: 10px 0;
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
</style>
@endsection


@section('js')
<script>
  $('#addTicket').on('click', function(e){


    $(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');

  });


  $('#assignTo').on('keyup', function(){

    var assignTo = $('#assignTo').val();

     $.ajax({
          type: 'get',
          url: '{{ URL::to('admin/searchUser') }}',
          data: { 'name' : assignTo },
          success: function(data) {
          
            var total = data.length;
            var output = '';

            console.log(data);
            if(data.length > 0) {

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
            console.log(error);
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

  });
</script>
@stop

