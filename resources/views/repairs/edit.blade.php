@extends('layouts.app')

@section('content')
  <div class="panel panel-top">
    <div class="row">
      <div class="col-sm-6">
        <button onclick="goBackTop({{ $repair->id }})"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-angle-left btn-icon"></i><span>Go Back</span></button>
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
          {{ Form::open(array('url' => '/update-repair/' . $repair->id )) }}

          <div class="row">
            <div class="col-sm-12">
              <div class="row"> 
                <div class="col-sm-6">
                  <div class="form-group row mt-10">
                    <label for="requested-date" class="col-sm-3 col-form-label">RMA #</label>
                    <div class="col-sm-9">
                      <p>R{{ $repair->id }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6">  
              <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group row mt-10">
                      <label for="requested-date" class="col-sm-3 col-form-label">Date Requested</label>
                      <div class="col-sm-9">
                        <input type="date"  name="requested_date" class="form-control form-control-plaintext" id="requested-date" value="{{ $repair->requested_date }}" max="{{ date('Y-m-d') }}" read-only>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="row">
                      <label for="requester-po-number" class="col-sm-3 col-form-label">PO Number</label>
                      <div class="col-sm-9">
                        <input type="text" name="requester_po_number" class="form-control form-control-plaintext" id="requester-po-number" value="{{ $repair->po_number  }}" required>
                      </div>
                    </div>
                  </div> 
                  <div class="col-sm-12">
                    <div class="form-group row mt-10">
                      <label for="currency" class="col-sm-3 col-form-label">Currency</label>
                      <div class="col-sm-9">
                        <select  id="currency" type="text" name="currency" value="{{ $repair->currency  }}" required>
                          <option value="">Select</option>
                          @php
                            $currencies = json_decode(config('constants.CURRENCIES'));
                          @endphp
                          @foreach ($currencies as $currency)
                            @if($currency === $repair->currency )
                              <option value="{{ $currency }}" selected>{{ $currency }}</option>
                            @else
                            <option value="{{ $currency }}">{{ $currency }}</option>
                            @endif

                            
                          @endforeach
                
                        </select>
                      </div>
                    </div>
                  </div> 
              </div>  
            </div>
            {{-- <div class="col-sm-6">
              @include('repairs/components/status')
            </div>   --}}

          </div>
          <br><br>
          <div class="row">
            <div class="item-list col-sm-6">
              <div class="heading"><h4>Requester:</h4></div>
              <div class="">
                <div class="form-group row">
                  <label for="requester-name" class="col-sm-3 col-form-label">Name <span class="text text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text"  class="form-control-plaintext" name="requester_name" id="requester-name" value="{{ $repair->requester_name }}" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="requester-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="tel" class="form-control"  name="requester_phone" id="requester-phone" value="{{ $repair->requester_phone}}" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="requester-company" class="col-sm-3 col-form-label">Company <span class="text text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="requester_company" id="requester-company" value="{{ $repair->requester_company}}" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="requester-email" class="col-sm-3 col-form-label">Email <span class="text text-danger">*</span></label>
                  <div class="col-sm-9">
                    <input type="email" class="form-control" name="requester_email" id="requester-email" value="{{ $repair->requester_email}}" required>
                    @if(Auth::user()->isAdmin())
                      <label class="switch">
                        <input type="checkbox" name="notify" value="1" {{ $repair->notify == 1 ? 'checked' : '' }}>
                        <span class="slider round"></span>
                      </label>
                      <span style="margin-left: 5px;">Notify</span>
                    @endif
                    
                  </div>
                </div>
                <div class="form-group row">
                  <label for="requester-fax" class="col-sm-3 col-form-label">Fax</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="requester_fax" id="requester-fax" value="{{ $repair->requester_fax}}">
                  </div>
                </div>
                
              </div>  <!-- Requester Row --> 
            </div>
            <div class=" item-list col-sm-6">
              <div class="heading"><h4>Delivery Address:</h4></div>
              <div class="">
                <div class="form-group row {{ $errors->has('country') ? ' has-error' : '' }}">
                  <label  for="country-input" class="col-sm-3 col-form-label">Country <span class="text text-danger">*</span></label>
                  @php
                  $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                  @endphp
                
                  <div class="col-sm-9">
                    <select name="country" type="text" id="country-input" class="form-control" required>
                        <option value="">Select</option>
                        @foreach($countries as $country)
                          @if($repair->country == $country)	
                            <option value="{{ $country }}" selected>{{ $country }}</option>
                          @else
                            <option value="{{ $country }}">{{ $country }}</option>
                          @endif	
                        @endforeach
                    </select>
                  </div>
                </div>
                  <div class="form-group row">
                    <label for="company-name" class="col-sm-3 col-form-label">Company Name <span class="text text-danger">*</span></label>
                    <div class="col-sm-9">
                      <input type="text"  class="form-control-plaintext"  name="company_name" id="company-name" value="{{ $repair->company_name }}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="company-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="company_phone" id="company-phone" value="{{ $repair->company_phone}}" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="company-fax" class="col-sm-3 col-form-label">Fax </label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="company_fax" id="company-fax" value="{{ $repair->company_fax}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="company-address" class="col-sm-3 col-form-label">Address <span class="text text-danger">*</span></label>
                    <div class="col-sm-9">
                      <textarea class="form-control" name="company_address" id="company-address">{{ $repair->company_address}}</textarea>
                    </div>
                  </div>

                </div>
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">  
                <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary" id="submitForm"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
              </div> 
        </div>            
        {!! Form::close() !!}

      </div>
  </div>

@endsection

@section('js')
<script>
    function goBack(id) {
        //e.preventDefault();
        window.location.href = "/repairs/" + id;
    }
    function goBackTop(id) {
      window.location.href = "/repairs/" + id;
    }

    $('#submitForm').on('click', function(){

        //$('button').attr('readonly', 'readonly');
        
        //$(this).after('<div class="form-spinner"><i class="fa fa-refresh fa-spin spin-loader" style="font-size:24px"></i></div>');

        

    })
</script>
@endsection

@section('css')
    <style>
    ul.list-inline {
        display: flex;
        flex-wrap: wrap;
    }    
    ul.list-inline li {
        padding: 5px 10px;
        background: #f5f5f5;
        margin-bottom: 5px;
        border-radius: 4px;
        margin-right: 5px;
    }
    ul.list-inline .radio {
        margin: 0;
    }
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
    .find-user label {
      font-weight: 400;
      color: #716e6e;
  }
  .find-user .heading,
  .item-list .heading {
      background: #d4edf9;
      padding: 7px 10px;
      margin-bottom: 20px;
      border-bottom: 2px solid #7fbddb;
  }
  .item-list .heading {
    margin-bottom: 10px;
  }
  .find-user .heading h4,
  .item-list .heading h4 {
      margin-bottom: 0;
      font-size: 16px;
      color: #156185;
  }
  .field {
    border-bottom: 0;
  }
</style>
@stop