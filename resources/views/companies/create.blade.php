@extends('layouts.app')

@section('content')
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('createCompany') !!} 
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
  	@include('components/errors')
    @component('components/panel')
        @slot('title')
           New Company
        @endslot
			
              {!! Form::open(['route' => 'companies.store','autocomplete' => 'off']) !!} 
                <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                  <label>Company <span class="text text-danger">*</span></label>
                  <input type="text" class="form-control" id="newCompanyName" name="name" required/>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <label>Email <span class="text text-danger">*</span></label>
                  <input type="text" class="form-control" name="email" id="emailInput" required>
                  <span class="emailValidation"></span>
                </div>
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                  <label>Description</label>
                  <input type="text" class="form-control" name="description"/>
                </div>
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                  <label>Country</label>
                  <select name="country" type="text" id="country-input" class="form-control">
                      <option value="">Select</option>
                      @include('profile/utilities/countries', ['current_country' => ''])
                  </select>
                </div>
                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                  <label>Address</label>
                  <input type="text" class="form-control" name="address" />
                </div>
                <div class="form-group{{ $errors->has('tel_no') ? ' has-error' : '' }}">
                  <label>Telephone no</label>
                  <input type="text" class="form-control" name="tel_no" />
                </div>
                <div class="form-group{{ $errors->has('fax') ? ' has-error' : '' }}">
                  <label>Fax</label>
                  <input type="text" class="form-control" name="fax" />
                </div>
                <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                  <label>Contact Person</label>
                  <input type="text" class="form-control" name="contact_person" />
                </div>
               
                <div class="form-group">
                    <label for="currency" class="col-form-label">Currency <span class="text text-danger">*</span></label>
                    <div class="">
                      <select  id="currency" type="text" name="currency" style="margin-bottom:15px;" value="" required>
                        <option value="">Select</option>
                        @php
                          $currencies = json_decode(config('constants.CURRENCIES'));
                        @endphp
                        @foreach ($currencies as $currency)
                          <option value="{{ $currency }}">{{ $currency }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
              
                <button type="submit" id="submitCompany" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check"></i><span>Add Company</span></button>
            
             </form> 
    @endcomponent 
@endsection