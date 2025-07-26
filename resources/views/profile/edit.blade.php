@extends('layouts.app')


@section('content')
  <div class="customer-section">
    
     @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            Edit Your Profile
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
    @endif
    @component('components.panel')
     
        @slot('title')
             <h3>Edit Your Profile</h3>
        @endslot      

        {!! Form::open(['route' => array('profile.update', $user->id),'method' => 'patch', 'class' => 'form-horizontal', 'files' => true ]) !!}
        <div class="register-section">

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
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="filePhoto" class="col-md-12">Profile Photo</label>
                        <div class="col-md-12">
                            <input id="filePhoto" type="file" class="form-control" name="photo" value="" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label for="firstname" class="col-md-12">First Name</label>
                        <div class="col-md-12">
                            <input id="firstname" type="text" class="form-control" name="firstname" value="{{ $user->firstname }}" required autofocus>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('lastname') ? ' has-error' : '' }}">
                        <label for="lastname" class="col-md-12">Last Name</label>
                        <div class="col-md-12">
                            <input id="lastname" type="text" class="form-control" name="lastname" value="{{ $user->lastname }}" required autofocus>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-12">E-Mail Address</label>
                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                    </div>                                
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                        <label for="text-c" class="col-md-12">Company</label>
                        <div class="col-md-12">
                            <input id="text-c" type="text" class="form-control" name="company" value="{{ $user->company }}" required>
                        </div>
                    </div>   
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                        <label for="country-input" class="col-md-12">Country</label>
                        <div class="col-md-12">
                            <select name="country" type="text" id="country-input" class="form-control" required>
                                <option value="">Select</option>
                                @include('profile/utilities/countries', ['current_country' => $user->country])
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Phone Number</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="phone" value="{{ $user->phone }}" required>
                        </div>
                    </div>  
                </div>
            </div>
            <hr />
            @if(!empty($usermeta) )
                @foreach($usermeta as $meta)
                   <?php 

                    $address      = $meta->address;
                    $address2     = $meta->address2;
                    $city         = $meta->city;
                    $state        = $meta->state;
                    $zipcode      = $meta->zipcode;
                    $fax          = $meta->fax;
                    $sms_number   = $meta->sms_number;
                    $office_phone = $meta->office_phone;
                    $website        = $meta->website;


                   ?>
                @endforeach
            @else
             <?php 

                    $address      = '';
                    $address2     = '';
                    $city         = '';
                    $state        = '';
                    $zipcode      = '';
                    $fax          = '';
                    $sms_number   = '';
                    $office_phone = '';
                    $website        = '';


                   ?>
             @endif  
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Address</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="address" value="{{ $address }}" />
                        </div>
                    </div>  
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Address 2</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="address2" value="{{ $address2 }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">City</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="city" value="{{ $city }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">State</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="state" value="{{ $state }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Zipcode</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="zipcode" value="{{ $zipcode }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Fax</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="fax" value="{{ $fax }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">SMS Number</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="sms_number" value="{{ $sms_number }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Office Phone</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="office_phone" value="{{ $office_phone }}" />
                        </div>
                    </div> 
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Website</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="website" value="{{ $website }}" />
                        </div>
                    </div> 
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">    
                    <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon">
                       <i class="fa fa-check"></i> <span>Save Changes</span>
                    </button>
                </div>    
            </div>
        {!! Form::close() !!} 
    
    @endcomponent

  </div>
@endsection


@section('css')
<style>
.register-section {
    padding: 20px;
}
select#role-input {
    text-transform: capitalize;
}
</style>
@stop
