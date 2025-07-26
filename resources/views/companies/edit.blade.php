@extends('layouts.app')


@section('content')
  <div class="customer-section">
    
     @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            Edit Company Info
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
    @endif
    @component('components.panel')
     
        @slot('title')
             <h3>Edit Company Info</h3>
        @endslot      

        {!! Form::open(['route' => array('companies.update', $company->id),'method' => 'patch', 'class' => 'form-horizontal', 'files' => true ]) !!}
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
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name" class="col-md-12">Name</label>
                        <div class="col-md-12">
                            <input id="name" type="text" class="form-control" name="name" value="{{ $company->name }}" required autofocus>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-12">E-Mail Address</label>
                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control" name="email" value="{{ $company->email }}" required>
                        </div>
                    </div>                                
                </div>

                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                        <label for="country-input" class="col-md-12">Country</label>
                        <div class="col-md-12">
                            <select name="country" type="text" id="country-input" class="form-control" required>
                                <option value="">Select</option>
                                @include('profile/utilities/countries', ['current_country' => $company->country])
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address" class="col-md-12">Address</label>
                        <div class="col-md-12">
                            <input id="address" type="text" class="form-control" name="address" value="{{ $company->address }}" >
                        </div>
                    </div>                                
                </div>
                <div class="col-sm-4">
                  <div class="form-group{{ $errors->has('telephone_no') ? ' has-error' : '' }}">
                        <label for="phone-input" class="col-md-12">Telephone Number</label>
                        <div class="col-md-12">
                            <input id="phone-input" type="text" class="form-control" name="telephone_no" value="{{ $company->telephone_no }}" >
                        </div>
                    </div>  
                </div>
                <div class="col-sm-4">
                    <div class="form-group{{ $errors->has('fax') ? ' has-error' : '' }}">
                    <label  class="col-sm-3 col-form-label">Fax</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="fax" value="{{ $company->fax }}" />
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                    <label  class="col-sm-12 col-form-label">Contact Person</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="contact_person" value="{{ $company->contact_person }}" />
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="currency" class="col-sm-3 col-form-label">Currency <span class="text text-danger">*</span></label>
                        <div class="col-sm-12">
                          <select  id="currency" type="text" name="currency" style="margin-bottom:15px;" value="" required>
                            <option value="">Select</option>
                            @php
                              $currencies = json_decode(config('constants.CURRENCIES'));
                            @endphp
                            @foreach ($currencies as $currency)
                                @if($currency == $company->currency) 
                                    <option value="{{ $currency }}" selected>{{ $currency }}</option>
                                @else
                                    <option value="{{ $currency }}" >{{ $currency }}</option>
                                @endif
                              
                            @endforeach
                          </select>
                        </div>
                      </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description" class="col-md-12">Description</label>
                        <div class="col-md-12">
                           <textarea name="description" id="" cols="30" rows="10">{{ $company->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <div class="col-md-12">    
                            <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon">
                               <i class="fa fa-check"></i> <span>Save Changes</span>
                            </button>
                        </div>    
                    </div>
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
