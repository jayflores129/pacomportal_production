@extends('layouts.app')

@section('content')
  <div class="panel panel-top">
    <div class="row">
      <div class="col-sm-6">
        <button onclick="goBackTop()"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-angle-left btn-icon"></i><span>Go Back</span></button>
      </div>
      <div class="col-sm-6 text-right">
      </div>
    </div>
  </div>
  <div class="panel panel-default panel-brand">
      <div class="panel-heading">
          <h3>Edit Repair</h3>
      </div>
      <div class="panel-body">
          @include('components/flash')
          @include('components/errors')
          {!! 
              Form::open([
                'method' => 'patch',
                'route' =>  ['repairs.customer-update-repair', $repair->id ],
                'autocomplete' => 'off'

                ]) !!}

               
                     <input type="hidden" class="form-control" id="input_cn" name="company" value="{{ $repair->company }}"/>
            

                     @if ($products)
                        <div class="col-md-6">  
                          <div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
                            <label for="input_pn">Choose a Product</label>
                            <select id="input_pn" name="product">
                                @foreach ($products as $product)
                                  @if ( $repair->product == $product->name )
                                    <option value="{{ $product->name }}" selected>{{ $product->name }}</option>
                                  @else
                                      <option value="{{ $product->name }}">{{ $product->name }}</option>
                                  @endif
                                @endforeach  
                            </select>
                          </div>
                        </div>
                    @endif
                    <div class="col-md-6">  
                      <div class="form-group{{ $errors->has('serial_no') ? ' has-error' : '' }}">
                        <label for="input_sn">Product Serial Number</label>
                        <input type="text" name="serial_no" class="form-control" id="input_sn" value="{{ $repair->product_serial_no }}" required>
                      </div>
                    </div>
                    @if ($issues)
                      <div class="col-md-6">  
                        <div class="form-group{{ $errors->has('issue') ? ' has-error' : '' }}">
                          <label for="input_i">Issue</label>
                          <select  id="input_i" name="issue">
                              @foreach ($issues as $issue)
                                  @if ( $repair->issue == $issue->name )
                                     <option value="{{ $issue->name }}" selected>{{ $issue->name }}</option>
                                  @else
                                       <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                                  @endif
                              @endforeach 
                          </select>
                        </div>
                      </div>
                    @endif
                    <div class="col-md-12">  
                      <div class="form-group{{ $errors->has('problem_description') ? ' has-error' : '' }}">
                        <label for="input-pd">Problem Description</label>
                        <textarea name="problem_description"  id="input-pd" rows="3" >{{ $repair->problem_description }}</textarea>
                      </div>
                    </div>

                      <div class="col-md-12 grid">  
                    
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary" id="submitForm"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
                      <a href="{{ route('repairs.show', ['id' => $repair->id]) }}" class="btn-brand btn-brand-icon btn-brand-danger"><i class="fa fa-close btn-icon"></i><span>Cancel</span></a>
                      </div> 
                  {!! Form::close() !!}

      </div>
  </div>

@endsection


@section('css')
<style>
    .panel .panel-header {
        padding: 10px;
        background: #2d2d2d;
    }
    .panel .panel-header .heading {
          margin: 0;
          font-size: 1.2em;
          color: #fff;
    }
    .form .radio {
      display: inline-block;
      margin: 10px 20px 10px 0;
    }
    .input-warranty input {
      margin-right: 10px;
    } 
    .list-inline > li {
        margin-right: 20px;
    }
    button {
      margin-right: 10px;
    }
</style>
@stop

@section('js')
<script>
    function goBack(e) {
        e.preventDefault();
        window.history.back();
    }
    function goBackTop(e) {
        window.history.back();
    }

    $('#submitForm').on('click', function(){

        $('button').attr('readonly', 'readonly');
        
        $(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>')

    })
</script>
@endsection