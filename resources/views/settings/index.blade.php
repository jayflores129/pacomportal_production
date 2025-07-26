@extends('layouts.app')

@section('content')

    @if( Auth::user()->isAdmin() )

      <div class="panel panel-top">

          <div class="row">

              <div class="col-sm-6">

                {!! Breadcrumbs::render('settings') !!}

              </div>
              <div class="col-sm-6 text-right">
              
              </div>

          </div>

      </div> 

    @endif
    @include('components/flash')

    <div class="panel panel-default panel-brand"> 

      <div class="panel-heading"><h3>Email </h3></div>

      <div class="panel-body">

          <div class="row">

            <div class="col-md-5">

               {!! Form::open(['route' => 'settings.store', 'autocomplete' => 'off']) !!}

              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group">
                  <label for="selectCat">Add email to receive notification for new user registration</label>
                  <input type="text" name="new_user_notification" class="form-control" value="{{ $user_email }}"/>
              </div>

              <div class="form-group">
                  <label for="selectCat">Add email to receive notification for new repairs</label>
                  <input type="text" name="new_repair_notification" class="form-control" value="{{ $repair_email }}"/>
              </div>

              <div class="form-group">
                  <label for="selectCat">Email to edit RMA (Peter)</label>
                  <input type="text" name="rma_editor" class="form-control" value="{{ $rma_editor }}"/>
              </div>

              <div class="form-group">
                <label for="selectCat">Add email to receive notification for new task</label>
                <input type="text" name="new_task_notification" class="form-control" value="{{ $task_email }}"/>
              </div>

              <div class="form-group">
                  <label for="selectCat">Default assignee for new task (Customers only)</label>
                  <select type="text" name="default_assignee">
                    @foreach($spg_users as $user)

                         <option value="{{ $user->id }}" {{ ($default_assignee == $user->id ) ? 'selected':''}}>{{ $user->firstname . ' '. $user->lastname }}</option>
                    @endforeach
                  </select>
              </div>

            </div>

            <div class="col-md-7">
              
              <div class="form-group">

                  <label for="GDPR">GDPR compliant content (This will be shown on the registration form)</label>
                  <textarea id="GDPR" name="GDPR" rows="50" style="min-height: 500px;padding:20px;">{{ $GDPR }}</textarea>

              </div>

            </div>

          </div>

          <div class="row">

            <div class="col-sm-5">
              <button type='submit'  class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
               {!! Form::close() !!}
            </div>

          </div>  

      </div>

    </div>

@endsection


@section('css')
<style>
   .grid .column {

      width: 25%;
   }
   .grid .column:nth-child(1) {
      width: 10%;
   }
   .grid .column:nth-child(2) {
      width: 50%;
   }
   .grid .column:nth-child(3) {
      width: 20%;
   }
   .grid .column:nth-child(4) {
      width: 20%;
   }
  .firmwares {
      padding: 30px;
  }
  .category {
      margin: 40px 0;
  }
  .category h3 {
    margin-bottom: 20px;
    color: #2680d0;
    font-size: 21px;
  }
  .releases {
    list-style: none;
    padding=left: 40px;
  }
  .releases h5 {
    font-size: 17px;
    color: #313131;
    font-weight: 600;
  }
  .firmware-list {
    border-bottom: 1px solid #f3f2f2;
    align-items: center;
    padding: 10px 0;
    margin-left: 100px;
  }
  .table-block {
    width: 100%;
  }
  .table-block tr td:first-child {
    vertical-align: top;
    padding-top: 15px;
  }
  .table-block td:first-child h5 {
    font-weight: 600;
    font-size: 16px;
  }
</style>
    
@stop
