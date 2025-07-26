@extends('layouts.app')

@section('content')

      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('addRepairs') !!} 
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
  

   @include('components/flash')  

   <div class="alert alert-danger hide" id="errorMessage"></div>
   <div class="alert alert-success hide" id="successMessage"></div>

 <div class="row form" id="wrap-form">
   <div class="col-lg-12">
      @component('components/panel')
        @slot('title')
          Submit A Request
        @endslot

          @include('components/errors')


          <div class="find-user">
            
            <form id="submitRepairForm">
              <div class="row">
                  <div class="col-md-6">

                      <div class="heading"><h4>Requester Details:</h4></div>

                      <div class="form-group row">
                        <label for="requester-name" class="col-sm-3 col-form-label">Name <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="text"  class="form-control-plaintext" id="requester-name" value="{{ $user->firstname . " " . $user->lastname }}" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="requester-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="tel" class="form-control" id="requester-phone" value="{{ $user->phone }}" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="requester-company" class="col-sm-3 col-form-label">Company <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="requester-company" value="{{ $user->company }}" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="requester-email" class="col-sm-3 col-form-label">Email <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="email" class="form-control" id="requester-email" value="{{ $user->email }}" required>
                          @if(Auth::user()->isAdmin())
                            <div style="display: flex;align-items:center;gap: 5px;">
                              <label class="switch">
                                <input type="checkbox" name="notify" id="notify" value="1">
                                <span class="slider round"></span>
                              </label>
                              <span>Notify</span>
                            </div>
                          @endif
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="requester-fax" class="col-sm-3 col-form-label">Fax</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="requester-fax" value="{{ $user->fax }}">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="requester-po-number" class="col-sm-3 col-form-label">P/O Number </label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" name="requester-po-number" id="requester-po-number" value="">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="date-requested" class="col-sm-3 col-form-label">Date Requested <span class="text text-danger">*</span><br></label>
                        <div class="col-sm-9">
                          <input type="date" class="form-control" name="date-requested" id="date-requested" value="<?php echo date("Y-m-d");?>" required>
                        </div>
                      </div>

                  </div>
                  <div class="col-md-6">
                    <div class="heading"><h4>Delivery Address:</h4></div>
              
                    <div class="form-group row">
                      <label for="company-name" class="col-sm-3 col-form-label">Company Name <span class="text text-danger">*</span></label>
                      <div class="col-sm-9">
                        @if($userCompanies)           
                              <div class="form-group">
                                  <select  id="company-name"  name="company_name" required>
                                      <option value="">Select</option>
                                      @foreach ($userCompanies as $company)
  
                                          <option value="{{ $company->company->id }}">{{ $company->company->name }}</option>
                                     
                                      @endforeach  
                                  </select>
                              </div>
                        @else 
                           <span class="text text-danger text-md">No company found. Pls. contact Pacom support</span>      
                        @endif
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="company-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="company-phone" value="{{ $user->myCompany->telephone_no }}" required>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="company-fax" class="col-sm-3 col-form-label">Fax </label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="company-fax" value="{{ $user->myCompany->fax }}">
                      </div>
                    </div>
                    <div class="form-group row {{ $errors->has('address') ? ' has-error' : '' }}">
                      <label  for="country-input" class="col-sm-3 col-form-label">Country <span class="text text-danger">*</span></label>
                      <div class="col-sm-9">
                        <select name="country" type="text" id="country-input" class="form-control" required>
                            <option value="">Select</option>
                            @include('profile/utilities/countries', ['current_country' => '{{ $user->myCompany->country  }}'])
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="company-address" class="col-sm-3 col-form-label">Address <span class="text text-danger">*</span></label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="company-address" required>{{ $user->myCompany->address }}</textarea>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="currency" class="col-sm-3 col-form-label">Currency <span class="text text-danger">*</span></label>
                      <div class="col-sm-9">
                        <select  id="currency" type="text" name="currency" style="margin-bottom:15px;" value="" required>
                          <option value="">Select</option>
                          @php
                            $currencies = json_decode(config('constants.CURRENCIES'));
                          @endphp
                          @foreach ($currencies as $currency)
                            <option value="{{ $currency }}">{{ $currency }}</option>
                          @endforeach
                        </select>
                        <button id="clearBtn">Clear</button>
                      </div>
                    </div>
    
                  </div>
              </div>


          </div><!-- Find User End -->
          <div class="add-item-popup hide">
            <div class="popup-block">
              <div class="popup-heading"><span>Add Item</span><button id="btn-close">x</button></div>
              <div class="popup-body">

                  <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
                      <label for="input_sn" class="col-sm-4 col-form-label">Serial Number <span class="text text-danger">*</span></label>
                      <div class="col-sm-8"><input type="text" name="serial_no" id="input_sn" value="{{ old('serial_no') }}">
                        <div class="serial-number-note"><small>Please see below serial number examples. Put "NA" if serial number is not available.</small></div>
                        <div class="sn-example">
                          <p>Pacom serial number example: 077G-2332-004500</p>
                          <p>SPG serial number example: 2326-026H000132</p>
                          <a  href="{{ url('serial-no-examples') }}" target="_blank"><i class="fa fa-info fa-sm"></i><span> Click here to see the screenshots</span></a>
                        </div>
                      </div>
                  </div>
                  {{-- <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
                    <label for="date_purchase_known" class="col-sm-4 col-form-label">Date Purchase Known?</label>
                    <div class="col-sm-8">
                      <input type="checkbox" name="date_purchase_known" id="date_purchase_known">
                    </div>
                  </div>
                  <div class="form-group row item_date_purchase hide {{ $errors->has('item_date_purchase') ? ' has-error' : '' }}">
                    <label for="item_date_purchase" class="col-sm-4 col-form-label">Enter the original Order Date</label>
                    <div class="col-sm-8">
                      <input type="date" id="item_date_purchase" class="form-control" name="item_date_purchase">
                    </div>
                  </div> --}}

                  <input type="hidden" name="user_id" id="userID" value="{{ $user->id }}" />
                  <input type="hidden" name="company_id" id="companyID"  value="{{ $user->company_id }}" />

                  @if ($products)
                      <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                        <label for="input_pn" class="col-sm-4 col-form-label">Model <span class="text text-danger">*</span></label>
                        <div class="col-sm-8"><select  id="input_pn" name="product">
                            <option value="">Select</option>

                            @foreach ($products as $product)
                              <option value="{{ $product->name }}">{{ $product->name }}</option>
                            @endforeach  

                        </select></div>
                      </div>
                  @endif
            
      
                  @if ($issues) 
                      <div class="form-group row {{ $errors->has('issue') ? ' has-error' : '' }}">
                        <label for="input_i"  class="col-sm-4 col-form-label">Fault Category <span class="text text-danger">*</span></label>
                        <div class="col-sm-8">
                          <select id="selectIssue" name="issue" multiple>
                            @foreach ($issues as $issue)
                                <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                            @endforeach 
                          </select><span class="text-sm text-dark text-fault-note">Hold CTR Key andf Left click to select multiple faults</span>
                          <div  class="form-group"><strong>Please specify additional comment</strong>
                              <div><textarea id="fault-comment"></textarea></div>
                              <span class="validate-feedback">
                                This field is required
                              </span>
                          </div>
                        </div>
                      </div>
                  @endif
                  <input type="hidden" name="itemIndex" id="itemIndex" value="" />
                  <input type="hidden" name="isEditing" id="isEditing" value="0" />
                  <div class="form-group row ">
                    <label for="input_sn" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-8"><button type="button" class="btn-brand btn-brand-icon btn-brand-primary" id="addProduct"><i class="fa fa-check"></i><span>Add Item</span></button></div>
                  </div>

             
              </div><!-- Pop up heading End --> 
            </div><!-- Pop up block End --> 
          </div><!-- Add item End --> 

          <div class="item-list">
            <div class="heading"><h4>Faulty Items:</h4></div>
             <button id="addItem" type="button">Add Item</button>
             <table id="table-product" class="table table-stripe">
                <thead>
                  <tr>
                    <th width="200">Serial No</th>
                    <th width="200">Model</th>
                    <th width="250">Fault</th>
                    <th width="150">Date Purchase Known</th>
                    <th width="150">Action</th>
                 </tr>
                </thead>
                <tbody></tbody>
             </table>
       
                <input type="hidden" name="status"  value="open" />
                <div class="hide loading"><div><img src="{{ asset('public/images/loading.gif') }}" /></div></div>
                 
                <div class="form-btn-wrap">
                  <button type="submit" id="submitRepair" class="btn-brand btn-brand-icon btn-brand-primary btn-main">Submit Request</button>
                </div>
              </div> <!-- Item List End --> 

          </div>  
      @endcomponent
   </div>
 </div>
 <form>
                         
      
@endsection



@section('js')

<script>
  selectIssue();
  createCompany();
  addCompany();
  searchCompany();
  searchUser();
   /**
   * Show Popup Add item
   */ 
   $('#addItem').on('click', function(e){
        e.preventDefault();
        $('.add-item-popup').removeClass('hide'); 
   })
   
   $('#clearBtn').on('click', function(e){
     e.preventDefault();
      $('#submitRepairForm').find('.form-control').val('');
      $('#submitRepairForm').find('.form-control-plaintext').val(''); 
      $('#submitRepairForm').find('select').val('');
   })
   $('#date_purchase_known').on('change', function(){
      var date_is_known = $(this).val();
      if($(this).is(":checked")) {
         $('.item_date_purchase').removeClass('hide');
         //$('#item_date_purchase').prop('required',true);
         //console.log('is checked');
      } else {
        $('.item_date_purchase').addClass('hide');
        //$('#item_date_purchase').prop('required',false);
      }
   })
   
  /**
   * Hide Popup Add item
   */ 
  $('#btn-close').on('click', function(e){
         e.preventDefault();
         $('.add-item-popup').addClass('hide');
         clearItemFields();
  }) 
  /**
   * Select issue from the form
   * @return string hide / show problem desc
   */
  function selectIssue() {

    $('#selectIssue').on('change', function(){

        var issue = $('#selectIssue :selected').text();

        if(issue == 'Other') {

          $('.problemDesc').removeClass('hide');
        }
        else {
          $('.problemDesc').addClass('hide');
        }


    });

  }



  var newList = [];
  var count = 0;
  var prodData;
  var has_product = false;
  let faultyItemIndex = -1;
  let faultIndexList = 0;

  $('.item-list').on('click', '.deleteFaultyBTN', function(e){
        e.preventDefault();
        let itemID = $(this).attr('data-index');

        let delConfirm = confirm("Are you sure you want to delete?")

        if(delConfirm !== true) {
          return;
        }
        $(this).closest('tr').remove();

  });

  $('.item-list').on('click', '.editFaultyBTN', function(e){
      $('.add-item-popup').removeClass('hide');
      let currIndex = $(this).closest('tr').attr("data-tr-id");
      let serial_no = $(this).attr('data-serial');
      let model = $(this).attr('data-model');
      let order_date = $(this).attr('data-order-date');
      let item_date_purchase = $(this).attr('item_date_purchase');
      let date_purchased = $(this).attr('data-purchase-known');
      let fault_comment = $(this).attr('data-fault-comment');
      let faults = $(this).attr('data-faults') ? $(this).attr('data-faults') : "";



      $('#input_pn').val( model );
      $('#input_sn').val( serial_no );
      $('#itemIndex').val( currIndex );
      $('#item_date_purchase').val(item_date_purchase);
      $('#date_purchase_known').checked = date_purchase_known === '1';

      $('#isEditing').val('1');

      if(date_purchased == '1') {
        $('#date_purchase_known').prop("checked", true); 
      }

      $('#fault-comment').val( fault_comment );
      
       
      document.querySelector('#addProduct span').innerHTML = 'Update Item';
      document.querySelector('.add-item-popup .popup-heading span').innerHTML = 'Edit Item';

      const faultsArray = JSON.parse(faults);

  
      faultsArray.forEach(function(item, index){
            $('#selectIssue').find('option[value="'+ item +'"]').prop('selected', true);
      }) 
  
  });


  $('#addProduct').on('click', function(e){
      
      e.preventDefault();

      var faultIndexList = $('#table-product tbody tr:last-child').index();
      var model = $('#input_pn').val(),
          serial = $('#input_sn').val(),
          selectedIssue = $('#selectIssue').val(),
          faultComment = $('#fault-comment').val(),
          date_purchase_known = $('#date_purchase_known').val(),
          searchResults = $('#searchResults'),
          otherSelected = isOther($('.add-item-popup #selectIssue'), $('.add-item-popup #fault-comment'))
          item_date_purchase = $('#item_date_purchase').val();

          checkField(model, '#input_pn', 'boolean');
          checkField(serial, '#input_sn', 'boolean');
          checkField(selectedIssue, '#selectIssue', 'length');

          if(!otherSelected){
            $('.add-item-popup #fault-comment').closest('.form-group').addClass('validate-error');
            $('.add-item-popup #fault-comment').removeAttr('required', 'required');
          } else {
            $('.add-item-popup #fault-comment').closest('.form-group').removeClass('validate-error');
          }

      if(model && serial && selectedIssue.length > 0 && otherSelected) {
      
        let product = model;
        let serial_number = serial;
        let fault_cat = selectedIssue;
        let date_purchase_known = $('#date_purchase_known').prop('checked') == 1 ? 1 : 0;
        let fault_comment = $('#fault-comment').val();
        let itemIndex = faultIndexList + 1;
        let item_date_purchase = $('#item_date_purchase').val();
        let isEditing = $('#isEditing').val() == "1" ? 1 : 0;

         if(isEditing == "0" || isEditing == "") {
           var curIndexItem = faultIndexList + 1;
         } else {
           var curIndexItem = $('#itemIndex').val();
         }

        const payload = {
          product,
          serial_number,
          fault_cat,
          date_purchase_known,
          item_date_purchase,
          fault_comment,
          curIndexItem,
          isEditing
        }

        addItemTable(payload, fault_cat, isEditing);

        if (curIndexItem > 0) {
    

          newList.push({
            product,
            serial: serial_number,
            fault_cat,
            date_purchase_known,
            item_date_purchase,
            fault_comment: fault_comment
          });

          //$('.add-item-popup').addClass('hide');

          clearItemFields();

        } else {      
          newList.push({
            product,
            serial: serial_number,
            fault_cat,
            date_purchase_known,
            item_date_purchase,
            fault_comment: fault_comment
          });

          clearItemFields();

        }

      }

      //return;

    });

    function checkField(field, el, type = 'boolean') {
      
      if(type == 'boolean') 
      {
        if(!field) {
          $(el).addClass('errors');
        } 
        else {
          if($(el).hasClass('errors')) {
            $(el).removeClass('errors');
          }
        } 
      } else {
        if(field.length < 1) {
          $(el).addClass('errors');
        }
        else {
          if($(el).hasClass('errors')) {
            $(el).removeClass('errors');
          }
        }
      }
 
    }

    function clearItemFields() {

          $('#input_pn').val('');
          $('#input_sn').val('');
          $('#selectIssue').val('');
          $('#fault-comment').val('');
          $('#itemIndex').val( '' );
          $('#date_purchase_known').val('');
          $('#item_date_purchase').val('');
          $('#date_purchase_known').prop('checked', false);
          $('#selectIssue option:selected').prop('selected', false);
          $('#isEditing').val('0');
    }

    function addItemTable(item, faults = '', isEditing = 0) {
  
        let el_raw = $('#table-product tbody');
        let output = '';
        let itemFaultsOutput = '';
        let warranty_display = '';
    
        if(faults != '') {
          //faults =JSON.parse(faults);
          faults.forEach(function(fault, index) {
            itemFaultsOutput += fault + '<br>';
          })
        }
        
        output = `
            <td>` + item.serial_number + `</td>
            <td>` + item.product + `</td>
            <td>` + itemFaultsOutput + `</td>
            <td>` + item.date_purchase_known + `</td>
            <td>
              <button class="editFaultyBTN btn btn-primary" 
                data-item-id="` + item.curIndexItem + `" 
                data-serial="` + item.serial_number + `" 
                data-model="` + item.product + `" 
                data-item_date_purchase="` +  item_date_purchase + `"
                data-purchase-known="` + item.date_purchase_known + `"
                data-faults='` + JSON.stringify(faults)  + `'
                data-fault-comment = "`+ checkEmpty(item.fault_comment) +`"
                ><span class="fa fa-edit"></span>
              </button> <button class="deleteFaultyBTN btn btn-danger" data-serial="` + item.serial_number + `" data-id="` + item.curIndexItem + `"><span class="fa fa-trash"></span></button>
            </td>
          `;

        if(isEditing) {
          el_raw.find('tr[data-tr-id="'+ item.curIndexItem +'"]').html(output);
        } else {
          el_raw.append(`<tr data-tr-id=`+ item.curIndexItem +` >` + output + `</tr>`);
        }
        
                
  }

  function checkEmpty($item) {
        if($item == '' || $item == undefined || $item ==  null || $item ==  'null') {
            return $item = '';
        }
        return $item;
  }

  $(".add-item-popup #selectIssue").change((e) => {
      hasOther($(".add-item-popup #selectIssue"), $(".add-item-popup #fault-comment"))
  })

   var prep_prod = '';

  $('#submitRepairForm').on('submit', (e) => {
    e.preventDefault();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var r_name =  $('#requester-name').val();
    var r_phone =  $('#requester-phone').val();
    var r_company =  $('#requester-company').val();
    var r_email =  $('#requester-email').val();
    var r_fax =  $('#requester-fax').val();

    var r_po_number =  $('#requester-po-number').val();

    var c_name =  $('#company-name').val();
    var c_phone =  $('#company-phone').val();
    var c_address =  $('#company-address').val();
    var c_fax =  $('#company-fax').val();
    var country =  $('#country-input').val();
    var currency =  $('#currency').val();
    var user_id =  $('#userID').val();
    var company_id =  $('#companyID').val();
      
    var date_requested =  $('#date-requested').val(); 

    $('.loading').removeClass('hide');

    var notify_label = $('.form').find('.notification-success');

    if (newList.length === 0) {
      const errorMessage = document.querySelector("#errorMessage");
      errorMessage.classList.remove('hide');
      $('.loading').addClass('hide');
      errorMessage.innerHTML = 'Please add Faulty Items.';
      return;
    }

    const payload = {
      'user_id' : user_id,
      'company_id' : company_id,
      'requester_name' : r_name,
      'requester_phone' : r_phone,
      'requester_company' : r_company,
      'requester_email' : r_email,
      'requester_fax' : r_fax,
      'po_number' : r_po_number,
      'date_requested' : date_requested,
      'company_name' : c_name,
      'company_phone' : c_phone,
      'country' : country,
      'currency' : currency,
      'company_fax' : c_fax,
      'company_address' : c_address,
      'items' : JSON.stringify(newList),
      'notify': document.getElementById('notify')?.checked ? 1 : 0
    };

    if(newList.length > 0) {
      errorMessage.classList.add('hide');
        $.ajax({
          type : 'post',
          url  : '{{URL::to('/rmaCreatebyCust')}}',
          data: payload,
          success: function (data) {
            if (data.success) {
              $('.loading').addClass('hide');

              const successMessage = document.querySelector("#successMessage");
              successMessage.classList.remove('hide');
              //successMessage.innerHTML = 'New Ticket Added Successfully. <a href="/repairs/' + data.id +'">View RMA</a>';
              successMessage.innerHTML = 'Repair ID <strong>R'+ data.id  +'</strong> was added successfully. <a href="/repairs/' + data.id +'">View RMA</a>';
              //window.location.href  = '/repairs';
            }

            // if(data == 'saved') {
            //   $('#table-product tbody').html("<tr><th>Product Name</th><th>Serial No</th><th>Issue</th></tr>");
            //   $('.loading').addClass('hide');
            //   $('#submitRepair').addClass('hide');

            //   window.location.href  = '{{ url("/repairs/create") }}';

            // }
                
          },
          error: function (data) {
            console.log('Error:', data);
            $('.loading').removeClass('hide');
          }
      });
    }

  });

/**
  Search User & Company
**/
function searchUser() {
  $(document).ready(function(){

    $('#company-name').on('change', function(){
        let selectedCompany = $(this).val();
        let link   = `{{ URL::to('find-user-company') }}/${selectedCompany}`;
  
        $.ajax({
            type: 'get',
            url: link,
            data: {
              'companyID' : selectedCompany,
            },
            success: function (data) {
              console.log(data[0]);
              if(data.length > 0) {
                $('#company-phone').val(data[0]['telephone_no']);
                $('#company-fax').val(data[0]['fax']);
                $('#country-input').val(data[0]['country']);
                $('#company-address').val(data[0]['address']);
                $('#currency').val(data[0]['currency']);
              }

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })
    $('#find_user').on('change', function(){
      
        let userID = $(this).val();
        let output = '';
        let link   = '{{ URL::to('admin/find-user-by-id') }}';
  
        $.ajax({
            type: 'get',
            url: link,
            data: {
              'id' : userID,
            },
            success: function (data) {
              //console.log(data);
              var user = data['user'][0];
              var company = data['company'][0];

              if(data['user'].length > 0) {
                  let fullname = user['firstname'] + " " + user['lastname'];
                  $('#requester-name').val(fullname);
                  $('#requester-phone').val(user['phone']);
                  $('#requester-company').val(user['company']);
                  $('#requester-email').val(user['email']);
                  $('#companyID').val(user['company_id']);
                  $('#userID').val(userID);
              }
              if(data['company'].length > 0) {
        
                  $('#company-name').val(company['name']);
                  $('#company-phone').val(company['telephone_no']);
                  $('#company-address').val(company['address']);
                  
              }

              
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });

      });

    });  
}

/**
 * Search Company
 * @return {[type]} [description]
 */

function searchCompany() {

  $('#searchCompany').keyup(function() {


      var company = $('#searchCompany').val();
      var output = '';
  
      if(company == '') {
        $('#searchResults').html('');
      }

      $.ajax({

          type: 'get',
          url: '{{URL::to('admin/searchCompany')}}',
          data: {
             'name' : company,
          },
          success: function (data) {
            //console.log(data);
            var table = data['table'];
            var data = data['companies'];
            var total = data.length;

            

            if(total > 0 ) {
                output += '<h4>Found '+ total +' results</h4>';
                output += '<div class="company-list">';
                for(var x = 0; x < total; x++) {

                  if( data[x]['company'] ) {
                     output += '<div class="single-company table-'+ table +'"id="'+ data[x]['id'] +'"><strong>' + data[x]['company'] + ' </strong> - ' + data[x]['firstname'] + ' ' + data[x]['lastname']  +' <button class="btn-selected-company btn-brand btn-brand-icon btn-brand-primary float-right"><i class="btn-icon fa fa-check"></i><span>Select</span></button></div>';
                  }  else {
                    output += '<div class="single-company table-'+ table +'" id="'+ data[x]['id'] +'"><strong>' + data[x]['name'] + ' </strong> - ' + data[x]['email'] +' <button class="btn-selected-company btn-brand btn-brand-icon btn-brand-primary float-right"><i class="btn-icon fa fa-check"></i><span>Select</span></button></div>';

                  } 

                }
                output += '</div>';  

                $('#searchResults').html(output);            

            } else if (total == 0 && company != '') {
               output += '<h4>No result found!</h4>';
               output += '<div class="company-list">';
               //output += '<div class="single-company warning" ><strong>' + company + '</strong> <span class="text">is not in our system. </span> <button id="createCompany" class="btn-brand btn-brand-icon btn-brand-success float-right"><i class="btn-icon fa fa-check"></i><span>create</span></button></div>';
               output += '<div class="single-company warning" style="text-transform:uppercase;"><strong>User doesn\'t exist</strong></div>';
               output += '</div>'; 

               $('#searchResults').html(output); 
            }

            
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });

  });
}

function createCompany()
{
  

  $('#searchResults').on('click', '#createCompany', function(e) {

      e.preventDefault();

      var company      = $('#searchCompany').val();
      var form         = $('.company-section').find('form');
       
 
      $('.company-section').removeClass('hide');
      form.find('#newCompanyName').val(company);
      form.find('#submitCompany').attr('disabled', true);
    
  });

 $('.company-section .fa-close').on('click', function(){

    $('.company-section').addClass('hide');

 });

 $('#emailInput').on('keyup', function(){
      //console.log('test');
     var email    = $('#emailInput').val();
     var is_valid = validateEmail(email);
     var info    =  $('.emailValidation');

     if(is_valid) {
        $('#submitCompany').attr('disabled', false);
        info.html('<span class="label label-success">Email is valid.</span>');
     } else {
        $('#submitCompany').attr('disabled', true);
        info.html('<span class="label label-danger">Email is invalid. Please make sure email is correct.</span>');
     }
 });

 $('#submitCompany').on('click', function(e){

    e.preventDefault();
    $(this).attr('disabled', true)

      var company_name = $('#newCompanyName').val();
      var email        = $('.company-section form').find('input[name="email"]').val();
      var description  = $('.company-section form').find('input[name="description"]').val();
      var country      = $('.company-section form').find('select[name="country"] option:selected').val();
      var address      = $('.company-section form').find('input[name="address"]').val();
      var tel_no       = $('.company-section form').find('input[name="tel_no"]').val();
      var searchResult = $('#searchResults').find('.single-company');
      var inputGroup   = $('#searchCompany').closest('.form-group');
      var searchInput  = inputGroup.find('#searchCompany');
      var listTitle = $('#searchResults').find('h4');


      var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

      $.ajax({
          type : 'POST',
          url  : '{{URL::to('/admin/storeCompany')}}',
          data: {
            '_token': CSRF_TOKEN,
             'company_name': company_name,
             'description' : description,
             'address' : address,
             'tel_no' : tel_no,
             'email' : email,
             'country' : country
          },
          success: function (data) {
              //console.log(data);

              if(data['status'] == 'success') {

                //window.location.href  = '/repairs/create';
                $('.company-section').addClass('hide');

                searchResult.removeClass('warning').addClass('selected-company new-company-selected').attr('id', data['id']);
                searchResult.find('.text').remove();
                inputGroup.find('#searchCompany').hide();
                inputGroup.find('label').addClass('hide');
                listTitle.text('New Company');
                
                $('#companyID').val(data['id']);
                $('#createCompany').removeClass('btn-brand-success').addClass('btn-brand-primary');
                $('#createCompany').find('span').text('created');
                $('#createCompany').attr('disabled', true);

              }
               
          },
          error: function (data) {
              console.log('Error:', data);
          }
        });

 }); 


}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function addCompany() 
{
    // Select Company
    $('#searchResults').on('click', '.btn-selected-company', function(e) 
    {

        e.preventDefault();

        var user_id = $(this).closest('.single-company').attr('id');

        $('#searchCompany').val(user_id);

        var controller = new TimelineMax();
        var btnSelected =  $(this);
        var companyList = $('.company-list');
        var inputGroup =  $('#searchCompany').closest('.form-group');
        var listTitle = $('#searchResults').find('h4');
        var inputCompany =  $('#searchCompany');
        var inputLabel =  $('#searchCompany').closest('.form-group').find('label');

        controller
                  .to(inputLabel, 0.2, { autoAlpha: 0, x : -20, height: 0, padding: 0, margin: 0 })
                  .to(inputCompany, 0.3, {autoAlpha: 0, x : -20, height: 0,padding: 0}, '-=0.2')
                  .to(btnSelected, 0.3, { backgroundColor: '#3b3e3c', onUpdate: updateBtn }, '-=0.2')



        function updateBtn()
        {
          btnSelected.html('<i class="btn-icon fa fa-check"></i><span>Selected</span>');
          btnSelected.closest('.single-company').addClass('selected-company');
          btnSelected.attr('disabled', true);
          listTitle.text('Company');
          companyList.find('.single-company').not('.selected-company').remove();
        
        }

    });

}
    // RMA REPAIR HELPER (ADD, UPDATE)
    const isOther = (selectField, faultComment) => {
      const selected = selectField.val();
      const hasIncluded = (selected.indexOf("Other") > -1);
      if ( hasIncluded && !faultComment.val()){
        faultComment.closest('validate-feedback').css("display", "block");
        faultComment.closest('.fault-block').find('.validate-feedback').css("display", "block");
        // $(faultComment + ' .validate-feedback').css("display", "block");
        return false;
      } else {
        return true;
      }
    }


    const hasOther = (selectField, faultComment) => {
      const selected = selectField.val()
        const hasIncluded = (selected.indexOf("Other") > -1);
        if(hasIncluded){
          faultComment.attr('required', 'required');
          faultComment.addClass('required');
          return true;
        } else {
          faultComment.removeAttr('required');
          faultComment.removeClass('required');
          faultComment.closest('validate-feedback').css("display", "none")
          // $(faultComment + ' .validate-feedback').css("display", "none");
          faultComment.closest('.form-group').removeClass('validate-error');
          return false;
        }
    }


</script>
@endsection

@section('css')
<style>
  /* Add item validation */
 .validate-feedback{
    display: none;
  }
  .validate-error .validate-feedback {
    display: block;
    color: #ef7140;
    font-size: 13px;
    margin-bottom: 15px;
  }
  .validate-error .required{
    /* border-color: #ef7140; */
    border: 1px solid #ef7140 !important;
    margin-bottom: 0;
  }
  .yesno-switch-cover span {
    display: flex;
    align-items: center;
    line-height: 1.5;
  }
  .yesno-switch-cover input {
    margin: 0px 5px 0 0;
  }
  button#editBTN {
    border: 0;
    margin: 0 10px;
    background: #007cbc;
    border-radius: 4px;
    color: #fff;
  }
  button#deleteBTN {
      border: 0;
      background: #d13221;
      border-radius: 4px;
      color: #fff;
  }
  .text-fault-note {
    font-size: 13px;
    color: #bbb6b6;
    display: block;
    margin-bottom: 13px;
  }
  select#selectIssue {
      height: 140px;
  }
  input#date_purchase_known,
  .input-checkbox {
      height: 15px !important;
  }
  .form-btn-wrap {
    display: flex;
    justify-content: flex-end;
  }
  button#submitRepair {
      padding: 10px 20px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
  }
  .sn-example {
    background: #fdf0f0;
    padding: 4px 7px;
    font-size: 12px;
    border-radius: 4px;
 }
  button#addItem {
      margin-bottom: 10px;
      border: 0;
      background: #007cbc;
      color: #fff;
      padding: 5px 10px;
      border-radius: 4px;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 1px;
      font-size: 12px;
  }
  .panel-body input,
  .panel-body select {
      height: 36px;
      background: #fff;
      margin-bottom: 0px;
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
  .add-item-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    z-index: 999;
  }
  .add-item-popup .popup-block {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      width: 600px;
  }
  .add-item-popup .popup-block .popup-heading {
    padding: 8px 10px;
    background: #f5f5f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #000;
  }
  .add-item-popup .popup-block .popup-body {
    padding: 20px;
  }
  .loading {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      color: #fff;
      z-index: 9999;
      background: rgba(251, 250, 250, 0.8);
  }
  .loading > div {
      position: absolute;
      top: 50%;
      left: 50%;
      max-width: 250px;
      padding: 50px;
      transform: translate(-50%, -50%);
  }
  .loading > div img {
      width: 100%;
  }
  .errors {
    border: 1px solid #f71616 !important;
  }
  button#btn-close {
    border: 0;
    background: #474343;
    border-radius: 3px;
    color: #fff;
    width: 29px;
    height: 25px;
    font-size: 16px;
  }
  #searchResults .single-company {
      padding: 6px 10px;
      background: #fbfbfb;
      border-bottom: 1px solid #eae8e8;
      margin-bottom: 5px;
  }
  #searchResults .single-company.warning {
    border: 2px solid #f9af93;
    background: #fff;
    border-radius: 5px;
  }
  #searchResults h4 {
      margin-top: 20px;
      font-size: 15px;
      color: #1d6fb8;
  }
  #searchResults .single-company:after {
      content: '';
      clear: both;
      display: block;
  }
  #searchResults .single-company strong {
      line-height: 35px;
      text-transform: capitalize;
  }
  .float-right {
    float: right;
  }
   .company-section {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(243, 243, 243, 0.8);
      z-index: 200;
  } 
  .company-section .panel {
      position: absolute;
      width: 400px;
      max-width: 500px;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      box-shadow: 0 10px 30px 0 #ddd;
  }
  .company-section .panel-heading button {
      background: transparent;
      border: 0;
  }
  @media only screen and (max-width: 600px) {

    .add-item-popup .popup-block {
        width: calc(100% - 30px);
    }
  }
</style>
@endsection
