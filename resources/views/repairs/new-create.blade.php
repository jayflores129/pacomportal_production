@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/virtual-select.min.css') }}">
<script src="{{ asset('js/virtual-select.min.js') }}"></script>

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
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                
                        @if ($users)
                                <div class="form-group">
                                  <label for="find_user">Select a user: </label>
                                  <select  id="find_user" name="user">
                                      <option value="">Select</option>
              
                                      @foreach ($users as $user)
                                          <option value="{{ $user->id }}">{{ $user->firstname }} {{ $user->lastname }}</option>
                                      @endforeach  
              
                                  </select>
                                </div>
                        @endif
                        
                    </div>
                </div>
            </div>
            <form id="submitRepairForm">
                <div class="row">
                    <div class="col-md-6">

                        <div class="heading"><h4>Requester Details:</h4></div>

                        <div class="form-group row">
                          <label for="requester-name" class="col-sm-3 col-form-label">Name <span class="text text-danger">*</span></label>
                          <div class="col-sm-9">
                            <input type="text"  class="form-control-plaintext" id="requester-name" value="" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="requester-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                          <div class="col-sm-9">
                            <input type="tel" class="form-control" id="requester-phone" value="" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="requester-company" class="col-sm-3 col-form-label">Company <span class="text text-danger">*</span></label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="requester-company" value="" required>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="requester-email" class="col-sm-3 col-form-label">Email <span class="text text-danger">*</span></label>
                          <div class="col-sm-9">
                            <input type="email" class="form-control" id="requester-email" value="" required>
                            <div style="display: flex;align-items:center;gap: 5px;">
                              <label class="switch">
                                <input type="checkbox" name="notify" id="notify" value="1" checked>
                                <span class="slider round"></span>
                              </label>
                              <span>Notify</span>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="requester-fax" class="col-sm-3 col-form-label">Fax</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="requester-fax" value="">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="requester-po-number" class="col-sm-3 col-form-label">P/O Number</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" name="requester-po-number" id="requester-po-number" value="">
                          </div>
                        </div>
                        <div class="form-group row">
                          <label for="date-requested" class="col-sm-3 col-form-label">Date Requested <span class="text text-danger">*</span><br></label>
                          <div class="col-sm-9">
                            <input type="date" class="form-control-date" name="date-requested" id="date-requested" max="{{ date('Y-m-d') }}" required>
                          </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                      <div class="heading"><h4>Delivery Address:</h4></div>

                      <div class="form-group row">
                        <label for="company-name" class="col-sm-3 col-form-label">Company Name <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="text"  class="form-control-plaintext" id="company-name" value="" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="company-phone" class="col-sm-3 col-form-label">Telephone <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="company-phone" value="" required>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="company-fax" class="col-sm-3 col-form-label">Fax </label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="company-fax" value="">
                        </div>
                      </div>
                      <div class="form-group row {{ $errors->has('address') ? ' has-error' : '' }}">
                        <label  for="country-input" class="col-sm-3 col-form-label">Country <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <select name="country" type="text" id="country-input" class="form-control" required>
                              <option value="">Select</option>
                              @include('profile/utilities/countries', ['current_country' => ''])
                          </select>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="company-address" class="col-sm-3 col-form-label">Address <span class="text text-danger">*</span></label>
                        <div class="col-sm-9">
                          <textarea class="form-control" id="company-address" required></textarea>
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
                    
                      <div class="form-group row">
                        <input type="hidden" class="form-control" id="searchCompany" name="company" value="" />
                      </div>

                    </div>
                </div>

                @include('repairs/create/add-company')
                @include('repairs/create/add-item')
                @include('repairs/create/item-list')

         
      @endcomponent
   </div>
 </div>
 <form>
                         
      
@endsection



@section('js')
<script>

  let options = <?= json_encode($products) ?>;

  VirtualSelect.init({
    ele: '#input_pn',
    options: options.map((item) => ({ label: item.name, value: item.name })),
  });

    $('#addItem').on('click', function(e){
          e.preventDefault();
          $('.add-item-popup').removeClass('hide');   
    })
   
    $('#clearBtn').on('click', function(e){
      e.preventDefault();
        $('#submitRepairForm').find('.form-control').val('');
        $('#submitRepairForm').find('.form-control-plaintext').val(''); 
    })

    //When fault category in use
    $(".edit-item-popup #selectIssue").change((e) => {
      hasOther($(".add-item-popup #selectIssue"), $(".edit-item-popup #fault-comment"))
    })

    $(".add-item-popup #selectIssue").change((e) => {
      hasOther($(".add-item-popup #selectIssue"), $(".add-item-popup #fault-comment"))
    })

    // RMA REPAIR HELPER (ADD, UPDATE)
    const isOther = (selectField, faultComment) => {
      const selected = selectField.val();
      const hasIncluded = (selected.indexOf("Other") > -1);
      if ( hasIncluded && !faultComment.val()){
        faultComment.closest('validate-feedback').css("display", "block");
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

    $('#btn-close').on('click', function(e){
          e.preventDefault();
          $('.add-item-popup').addClass('hide');
          clearItemFields();
    }) 
    $('#btn-close-company').on('click', function(e){
          e.preventDefault();
          $('.add-company-popup').addClass('hide');
          $('#submitRepairForm').find('.form-control').val('');
         $('#submitRepairForm').find('.form-control-plaintext').val(''); 
          $('#find_user').val("");
    }) 

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
        let repair_cost = $(this).attr('data-repair-cost');
        let order_date = $(this).attr('data-order-date');
        let under_warranty = $(this).attr('data-under-warranty');
        let invalid_serial = $(this).attr('data-invalid-serial');
        let status = $(this).attr('data-status');
        let date_purchased = $(this).attr('data-purchase-known');
        let fault_comment = $(this).attr('data-fault-described');
        let faults = $(this).attr('data-faults') ? $(this).attr('data-faults') : "";

        $('#input_pn').val( model );
        $('#input_sn').val( serial_no );
        $('#itemIndex').val( currIndex );
        $('#date_purchase_known').checked = date_purchase_known === '1';

        $('#isEditing').val('1');

        if(date_purchased == '1') {
          $('#date_purchase_known').prop("checked", true); 
        }
        if(invalid_serial == '1') {
          $('#invalid_serial').prop("checked", true); 
        }
        
        $('input[value="' + under_warranty + '"]').prop("checked", true); 
        
        $('#original_order_date').val( order_date );
        $('#fault-comment').val( fault_comment );
        $('#repair_cost').val( repair_cost );
        
        
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
          invalid_serial = $('#invalid_serial').val(),
          warranty_flag = $('input[name="warranty_flag"]:checked').val(),
          original_order_date = $('#original_order_date').val(),
          repair_cost = $('#repair_cost').val(),
          searchResults = $('#searchResults'),
          otherSelected = isOther($('.add-item-popup #selectIssue'), $('.add-item-popup #fault-comment'));
     
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
        let under_warranty = $('input[name="warranty_flag"]:checked').val() == undefined ? "" : $('input[name="warranty_flag"]:checked').val();
        let invalid_serial = $('#invalid_serial').prop('checked') == 1 ? 1 : 0;
        let original_order_date = $('#original_order_date').val();
        let repair_cost = $('#repair_cost').val();
        let fault_comment = $('#fault-comment').val();
        let itemIndex = faultIndexList + 1;
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
          under_warranty,
          invalid_serial,
          original_order_date,
          repair_cost,
          fault_comment,
          curIndexItem,
          isEditing
        }
   
        addItemTable(payload, fault_cat, isEditing);

        if (curIndexItem > 0) {
    
          // newList[curIndexItem].product = product;
          // newList[curIndexItem].serial = serial_number;
          // newList[curIndexItem].fault_cat = fault_cat;
          // newList[curIndexItem].date_purchase_known = date_purchase_known;
          // newList[curIndexItem].under_warranty = under_warranty;
          // newList[curIndexItem].invalid_serial = invalid_serial;
          // newList[curIndexItem].original_order_date = original_order_date;
          // newList[curIndexItem].repair_cost = repair_cost;
          // newList[curIndexItem].fault_comment = fault_comment;

          newList.push({
            product,
            serial: serial_number,
            fault_cat,
            date_purchase_known,
            under_warranty,
            invalid_serial,
            original_order_date: original_order_date,
            repair_cost: repair_cost,
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
            under_warranty,
            invalid_serial,
            original_order_date: original_order_date,
            repair_cost: repair_cost,
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
          $('#original_order_date').val('');
          $('#repair_cost').val('');
          $('#itemIndex').val( '' );
          $('#date_purchase_known').val('');
          $('#date_purchase_known').prop('checked', false);

          $('#invalid_serial').val('');
          $('#invalid_serial').prop('checked', false);

          $('input[name="warranty_flag"]').prop('checked', false);
          $('#selectIssue option:selected').prop('selected', false);
          $('#isEditing').val('0');
          document.querySelector('#input_pn').setValue('');
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
        
        if(item.repair_cost === null || item.repair_cost === "" || item.repair_cost === undefined ){
          item.repair_cost = "";
        } else {
          item.repair_cost = item.repair_cost;
        }
        if(item.under_warranty === "" ){
          warranty_display = "";
        } 
        else if(item.under_warranty == "0") {
          warranty_display = "N";
        }  else if(item.under_warranty == "1") {
          warranty_display = "Y";
        }
        if(item.original_order_date === null || item.original_order_date === "" || item.original_order_date === undefined ){
          item.original_order_date = "";
        } else {
          item.original_order_date = item.original_order_date;
        }
        
        output = `
            <td>` + item.serial_number + `</td>
            <td>` + item.product + `</td>
            <td>` + itemFaultsOutput + `</td>
            <td>` + item.repair_cost + `</td>
            <td>` + item.original_order_date + `</td>
            <td>` + warranty_display + `</td>
            <td>
              <button class="editFaultyBTN btn btn-primary" 
                data-item-id="` + item.curIndexItem + `" 
                data-serial="` + item.serial_number + `" 
                data-model="` + item.product + `" 
                data-repair-cost="` + checkEmpty(item.repair_cost) + `" 
                data-order-date="` + item.original_order_date + `" 
                data-under-warranty="` + item.under_warranty + `" 
                data-invalid-serial="` + item.invalid_serial + `" 
                data-status="` + checkEmpty(item.status) + `" 
                data-purchase-known="` + item.date_purchase_known + `"
                data-faults='` + JSON.stringify(faults)  + `'
                data-fault-described = "`+ checkEmpty(item.fault_described_by_customer) +`"
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
        'notify' : document.getElementById('notify')?.checked ? 1 : 0,
        'items' : JSON.stringify(newList)
      };

      if(newList.length > 0) {
        errorMessage.classList.add('hide');
          $.ajax({
            type : 'post',
            url  : '{{URL::to('/store-rma')}}',
            data: payload,
            success: function (data) {
              if (data.success) {
                $('.loading').addClass('hide');
            
                const successMessage = document.querySelector("#successMessage");
                successMessage.classList.remove('hide');
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


    $('#find_user').on('change', function(){

      //Clear all values first
      $('#submitRepairForm').find('.form-control').val('');
      $('#submitRepairForm').find('.form-control-plaintext').val(''); 

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
                $('#companyID').val(company['id']);
                $('#company-name').val(company['name']);
                $('#company-phone').val(company['telephone_no']);
                $('#company-address').val(company['address']);
                $('#country-input').val(company['country']);
                
            } else {
              $('.add-company-popup').removeClass('hide');
              $('#addCompanyLink').attr('href', "{{ url('/admin/users/') }}" + "/" + userID );
            }
           
            
          },
          error: function (data) {
              console.log('Error:', data);
          }
      });

    });

    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

</script>
@endsection

@section('css')
<style>
  input#date-requested {
    width: 100%;
    height: 38px;
    padding: 0 10px;
    margin-bottom: 10px;
    border: 1px solid #e7e7e7;
    background: #fbfbfb;
    box-shadow: none;
}
  select#selectIssue option:checked {
      background: #e3dede !important;
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
  .serial-number-note span {
    display: inline-block;
    width: 12px;
    height: 12px;
    background: #e9e4e4;
    text-align: center;
    border-radius: 50%;
    font-size: 10px;
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
  .add-item-popup,
  .edit-item-popup, .add-company-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.3);
    z-index: 999;
  }
  .add-item-popup .popup-block, .add-company-popup .popup-block,
  .edit-item-popup .popup-block {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      width: 600px;
  }
  .add-item-popup.admin .popup-block{
    width: 98%;
    max-width: 900px;
  }
  .add-item-popup.admin .popup-block .btn-wrap {
    display: flex;
    justify-content: end;
  }
  .add-item-popup .popup-block .popup-heading,
  .edit-item-popup .popup-block .popup-heading,
  .add-company-popup .popup-block .popup-heading {
    padding: 8px 10px;
    background: #f5f5f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #000;
  }
  .add-item-popup .popup-block .popup-body,
  .edit-item-popup .popup-block .popup-body,
  .add-company-popup .popup-block .popup-body {
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
  select#edit_selectIssue {
      min-height: 130px;
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
  .sn-example {
      background: #fdf0f0;
      padding: 4px 7px;
      font-size: 12px;
      border-radius: 4px;
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
  textarea#fault-comment {
      background: #fff;
      padding: 10px;
  }

  @media only screen and (max-width: 600px) {

    .add-item-popup .popup-block {
        width: calc(100% - 30px);
       
    }
 
  }
  @media only screen and (max-width: 800px){
    .add-item-popup .popup-block {
      max-height: 600px;
      overflow: scroll;
    }
  }
</style>
@endsection
