<div class="search-filter-group hide" id="editSearch">
    <div class="search-rma-section">
      <div class="row">
         <div class="col-md-12">
             @include('components/flash')
             @include('components/errors')
          
             @if($editSearch != false) 
              {!! Form::open([
                'method' => 'delete',
                'route' => ['search.destroy', $editSearch->id]
               ]) !!}

                        <button type="submit" class="btn-brand btn-brand-primary" id="submitDelForm"><i class="fa fa-trash"></i></button>

              {!! Form::close() !!}
            
             {{ Form::open(array('url' => '/advanced-search-rma/', 'method' => 'get'  )) }}
                 <input type="hidden" name="is_search" value="1">
  
                 <div class="row">
                     
                        <div class="col-md-12">
                          <div class="form-group row">
                            <label for="company-address" class="col-sm-12 col-form-label">Filter Name</label>
                            <div class="col-sm-4">
                                <input type="text" name="name" class="form-control" value="{{ $editSearch->name }}" required/>
                            </div>
                          </div>
                        </div>            
                        @if ($users)
                            @if(Auth::user()->isAdmin())
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label for="company-address" class="col-sm-12 col-form-label">Requester Name</label>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <select  id="find_user" name="requester_id">
                                                    <option value="">Select</option>
                                                    @foreach ($users as $user)
                                                        @if($user->id == $editSearch->requester_id)
                                                            <option value="{{ $user->id }}" selected>{{ $user->firstname }} {{ $user->lastname }}</option>
                                                        @else
                                                            <option value="{{ $user->id }}">{{ $user->firstname }} {{ $user->lastname }}</option>
                                                        @endif
                                                    @endforeach  
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else 
                                <div class="col-sm-12">
                                    <input type="hidden" id="company_name" name="company_name" value="{{ Auth::user()->id }}"  readonly/> 
                                </div>
                            @endif
                        @endif
                         <div class="col-md-4">
                             <div class="form-group row">
                                 <label for="company_name" class="col-sm-12 col-form-label">Company Name</label>
                                 <div class="col-sm-12">
                                     @if ($companies && $userCompanies == false)
                                        <div class="form-group">
                                            <select  id="company_name" name="company_name">
                                                <option value="">Select</option>
                                                @foreach ($companies as $company)
                                                    @if($company->id == $editSearch->requester_company )
                                                    <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                                    @else
                                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endif
                                                    
                                                @endforeach  
                                            </select>
                                        </div>
                                     @elseif($userCompanies)           
                                            <div class="form-group">
                                                <select  id="company_name" name="company_name">
                                                    <option value="">Select</option>
                                                    @foreach ($userCompanies as $company)
                                                          
                                                        @if($company->company->id == $editSearch->requester_company )
                                                        <option value="{{ $company->company->id }}" selected>{{ $company->company->name }}</option>
                                                        @else
                                                        <option value="{{ $company->company->id }}">{{ $company->company->name }}</option>
                                                        @endif
                                                        
                                                    @endforeach  
                                                </select>
                                            </div>
                                     @endif
                                 </div>
                             </div>
                         </div>
                      {{-- @else
                        <input type="hidden" id="company_name" name="company_name" value="{{ $user->myCompany->id }}" />    --}}
                      
                      <input type="hidden" id="filter_type" name="filter_type" value="edit" />  
                      <input type="hidden" id="search_id" name="search_id" value="{{ $editSearch->id }}" /> 
                      <div class="col-md-4">
                         <div class="form-group row">
                             @php
                                 $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
                             @endphp
                         
                             <label for="company-address" class="col-sm-12 col-form-label">Country </label>
                             <div class="col-sm-12">
                                 <select name="country" type="text" id="country-input" class="form-control">
                                     <option value="">Select</option>
                                     @foreach($countries as $country)
                                          @if($country == $editSearch->country )
                                             <option value="{{ $country }}" selected>{{ $country }}</option>
                                          @else
                                            <option value="{{ $country }}">{{ $country }}</option>
                                          @endif
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                      </div>
                 </div>
  
                <div class="row">
                     <div class="col-md-4">
                         <div class="form-group row">
                             <label for="company-address" class="col-sm-12 col-form-label">PO Number</label>
                             <div class="col-sm-12">
                                 <input type="text" name="po_number" class="form-control" value="{{ $editSearch->po_number }}" />
                             </div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="form-group row">
                             <label for="company-address" class="col-sm-12 col-form-label">RMA Status</label>
                             <div class="col-sm-12">
                                 @php  $rma_status = ["Open","To Be Confirmed", "Confirmed", "Received","Completed","Shipped","Cancelled"] @endphp
                                 @if ($rma_status)
                                         <div class="form-group">
                                             <select  id="find_user" name="status">
                                                 <option value="">Select</option>
                                                 @foreach ($rma_status as $status)
                                                    @if($status == $editSearch->rma_status )
                                                      <option value="{{ $status }}" selected>{{ $status }}</option>
                                                    @else
                                                      <option value="{{ $status }}">{{ $status }}</option>
                                                    @endif
                                                 @endforeach  
                                             </select>
                                         </div>
                                 @endif
                             </div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="form-group row">
                             <label for="company-address" class="col-sm-12 col-form-label">Serial Number</label>
                             <div class="col-sm-12">
                                 <input type="text" name="serial_number" class="form-control" value="{{ $editSearch->serial_number }}" />
                             </div>
                         </div>
                     
                     </div>
             </div>
             <div class="row">
                 <div class="col-md-4">
                     <div class="form-group row">
                         <label for="company-address" class="col-sm-12 col-form-label">Model: </label>
                         <div class="col-sm-12">
                             <select name="model" type="text" class="form-control">
                                 <option value="">Select</option>
                                 @foreach($products as $product)
                                    @if( $product->name == $editSearch->model )
                                      <option value="{{ $product->name }}" selected>{{ $product->name }}</option>
                                    @else
                                      <option value="{{ $product->name }}">{{  $product->name }}</option>
                                    @endif
                                 @endforeach
                             </select>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-8">
                    <div class="form-group row">
                      <div class="col-md-12 date-range">
                          <label for="company-address" class="col-form-label">Date Range </label>
                          <div class="row">
                              <div class="col-md-6"><span>From: <input type="date" name="from" class="form-control" value="{{ $editSearch->date_from }}" /></span> </div>
                              <div class="col-md-6"><span>to: <input type="date" name="to" class="form-control" value="{{ $editSearch->date_to }}" /></span></div>    
                          </div>
                      </div>
                  </div>
                 </div>
                 
            </div>
                       
               
             
  
             </div>
             <div class="col-md-12">
                 <div class="row">
                     <div class="col-md-12 btn-filter-group">  
                        <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary" id="submitForm"><i class="fa fa-check btn-icon"></i><span>Submit</span></button>
                     </div> 
                 </div> 
                 {!! Form::close() !!}
                 @endif
             </div>
         </div>
     </div>
  </div>