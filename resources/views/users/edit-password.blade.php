@extends('layouts.app')

@section('content')
    @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
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
    @include('components/errors')
    <div class="panel panel-default panel-brand"> 
      <div class="panel-heading"><h3>Settings</h3></div>  
      <div class="panel-body">
          <div class="row">
            <div class="col-sm-5">

                <strong class="label label-info" style="font-size: 17px;color: #222;margin-bottom: 10px;display:inline-block;">You are about to change the password for {{ $user->firstname }} {{ $user->lastname }}  with a user id # {{ $user->id}}. Email Address: {{ $user->email }}</strong>
    
              {!! Form::open([
                    'route' => array('users.update_password', $user->id),
                    'method' => 'patch', 
                    'class' => 'form-horizontal' 
              ]) !!}

              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <label for="selectCat">New Password</label>
              <div class="form-group-area {{ $errors->has('password') ? ' has-error' : '' }}" style="margin-bottom: 10px;">
                  <input type="password" name="password" class="form-control" />
              </div>

              <div class="form-group-area" style="margin-bottom: 10px;">
                  <label for="selectCat">Retype Password</label>
                  <input type="password" name="password_confirmation" class="form-control" />
              </div>
             
              <div class="form-group-area" style="margin-bottom: 10px;">
                  
                      <strong>Note</strong>
                      <ul>
                          <li>Password must contain at least 6 characters.</li>   
                      </ul>
                
              </div>

              <div class="form-group-area mb-10" style="margin-bottom: 10px;">

                  <span>Password Hint :</span>
                  <input id="password-hint" type="text" class="form-control" name="password_hint">
                  
              </div>                                
               

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
