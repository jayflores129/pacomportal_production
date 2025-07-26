@extends('layouts.app')


@section('content')
  <div class="customer-section">
    
     @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('addCustomer') !!}
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
    @endif
    @include('components/flash')
    @component('components.panel')
     
        @slot('title')
             <h3>Registration</h3>
        @endslot      

        {!! Form::open(['route' => 'users.store','class' => 'form-horizontal', 'autocomplete' => 'off' ]) !!}
        <div class="register-section">
            <div class="row">
                <div class="col-sm-8">

                    {{ csrf_field() }}

                    @if( $errors->any() )
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error) 
                                  <li>{{ $error }}</li>
                                 @endforeach 
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="firstname" class="col-md-12">First Name</label>
                                <div class="col-md-12">
                                    <input id="firstname" type="text" class="form-control" name="firstname" value="{{ old('firstname') }}" required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="lastname" class="col-md-12">Last Name</label>
                                <div class="col-md-12">
                                    <input id="lastname" type="text" class="form-control" name="lastname" value="{{ old('lastname') }}" required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password" class="col-md-12">Password</label>
                                <div class="col-md-12">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                </div>
                            </div>                                
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password-confirm" class="col-md-12">Confirm Password</label>
                                <div class="col-md-12">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>                                
                        </div>
                    </div>
                        <strong>Note</strong>
                        <ul>
                            <li>Password must contain at least 6 characters</li>   
                            <li>Password must include at least one number</li>
                        </ul>
                    <hr />
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email" class="col-md-12">E-Mail Address</label>
                                <div class="col-md-12">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>                                
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="form-group">
                                <label for="text-c" class="col-md-12">Company</label>
                                <div class="col-md-12">
                                    <select id="text-c" type="text" class="form-control" name="company" required>
                                            <option value="">Select</option>
                                            @foreach($companies as $company)

                                                @if( old('company') == $company->id ) 
                                                    <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                                @else
                                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                @endif
                                            @endforeach
                                    </select>

                                </div>
                            </div>   
                        </div> --}}
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country-input" class="col-md-12">Country</label>
                                <div class="col-md-12">
                                    <select type="text" name="country" class="form-control">
                                            <option value="">Select</option>
                                            @if( old('country') ) 
                                                <option value="{{old('country') }}" selected>{{ old('country') }}</option>
                                                @include('components/countries')
                                            @else
                                                @include('components/countries')
                                            @endif
                                    </select>
                                </div> 
                            </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                                <label for="phone-input" class="col-md-12">Phone Number</label>
                                <div class="col-md-12">
                                    <input id="phone-input" type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                                </div>
                            </div>  
                        </div>
                    </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label for="role-input" class="col-md-12">User Role</label>
                    <div class="col-md-12">
                        @if($roles)
                        <select type="text" name="role" id="role-input" class="form-control" required>
                            <option value="">Select</option>
                            @foreach( $roles as $role)
                                <option value="{{ $role->name }}" {{ ( old('country') == $role->id ) ? 'selected': '' }}>{{ $role->name}}</option>
                            @endforeach
                            
                        </select>
                        @endif
                    </div>

                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="checkbox" name="subscribe" checked="checked"/> <span>Subscribe to software updates</span>
                    </div>
                </div>
            </div> 
            <div class="col-sm-6">
                <div class="form-group">
                    <div class="col-md-12">    
                        <button type="submit" class="btn btn-primary btn-submit">
                            Register
                        </button>
                    </div>    
                 </div>
            </div>
            {!! Form::close() !!}  
        </div>       
    </div>
    @endcomponent

  </div>
@endsection


@section('css')
<style>
.register-section {
    padding: 30px;
    border: 1px solid #f3f0f0;
}
select#role-input {
    text-transform: capitalize;
}
</style>
@stop
