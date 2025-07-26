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
   <div class="company-section hide">
      @component('components/panel')
        @slot('title')
           New Company
           <button class="float-right"><span class="fa fa-close"></span></button>
        @endslot
          
             @if( Auth::user()->isAdmin() )

              <form autocomplete="off">
              {{--       {!! Form::open(['route' => 'company.store','autocomplete' => 'off']) !!} --}}
                <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                  <label>Company (required)</label>
                  <input type="text" class="form-control" id="newCompanyName" name="name" readonly="readonly" required/>
                </div>
                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                  <label>Email (required)</label>
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
                <button type="submit" id="submitCompany" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check"></i><span>Add New</span></button>
            
             </form> 
          @endif

      @endcomponent  
  </div> 
 <div class="row form" id="wrap-form">
   <div class="col-lg-5">
      @component('components/panel')
        @slot('title')
          Submit A Request
        @endslot

          @include('components/errors')

           {!! Form::open(['route' => 'repairs.store','autocomplete' => 'off']) !!}

              @if( Auth::user()->hasRole('customer') )

                <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                  <input type="hidden" class="form-control" id="searchCompany" name="company" value="{{ Auth::user()->id }}" />
                </div>
   
              @else
    
                <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                    <label for="input_cn">Search Company</label>
                    <input type="text" class="form-control" id="searchCompany" name="company"  />
                    <div id="searchResults"></div> 
                    <input type="hidden" name="company_id" id="companyID" />
                </div>

              @endif


               @if ($products)
                    <div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
                      <label for="input_pn">Choose a Product</label>
                      <select  id="input_pn" name="product">
                          <option value="">Select</option>

                          @foreach ($products as $product)
                            <option value="{{ $product->name }}">{{ $product->name }}</option>
                          @endforeach  

                      </select>
                    </div>
              @endif
        
                <div class="form-group{{ $errors->has('serial_no') ? ' has-error' : '' }}">
                  <label for="input_sn">Product Serial Number</label>
                  <input type="text" name="serial_no" class="form-control" id="input_sn" value="{{ old('serial_no') }}">
                </div>
   

              @if ($issues) 
                  <div class="form-group {{ $errors->has('issue') ? ' has-error' : '' }}">
                    <label for="input_i">Issue</label>
                    <select id="selectIssue" name="issue">
                        @foreach ($issues as $issue)
                             <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                        @endforeach 
                    </select>
                    <br>
                  </div>
              @endif

              <button  class="btn-brand btn-brand-icon btn-brand-success" id="addProduct"><i class="fa fa-check"></i><span>Add Product</span></button>
      @endcomponent
   </div>
   <div class="col-lg-7">
      @component('components/panel')
          @slot('title')
              Tickets Table
          @endslot

             <table id="table-product" class="table table-stripe">
                <tr>
                   <th>Product Name</th>
                   <th>Serial No</th>
                   <th>Issue</th>
                </tr>
             </table>
       
              <input type="hidden" name="status"  value="open" />
            
               <div class="hide loading"><div><img src="{{ asset('public/images/loading.gif') }}" /></div></div>
                <button type="submit" id="submitRepair" class="hide btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check"></i><span>Submit</span></button>
             </div>   
              
      @endcomponent
   </div>
 </div>
 
  {!! Form::close() !!}
                         
      
@endsection

@section('css')
<style>
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
</style>
@endsection

@section('js')

<script>
  selectIssue();
  createCompany();
  addCompany();
  searchCompany();


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


  var newList = [[]];
  var count = 0;
  var prodData;
  var has_product = false;

  $('#addProduct').on('click', function(e){

    e.preventDefault();

     var product = $('#input_pn :selected').val(),
         serial = $('#input_sn').val(),
         selectedIssue = $('#selectIssue :selected').val(),
         prodList = $('#prodList')
         probDesc = $('#input-pd').val(),
         companyID = $('#searchCompany').val(),
         searchResults = $('#searchResults');

   
      if(!companyID) {
         $('#searchCompany').addClass('errors');
      }   
      else {
        if($('#searchCompany').hasClass('errors')) {
           $('#searchCompany').removeClass('errors');
        }
      }


      if( selectedIssue == 'other' ) {
         selectedIssue = selectedIssue + " : " + probDesc;
      }  

      if(!product) {

        $('#input_pn').addClass('errors');
      }  
      else {
        if($('#input_pn').hasClass('errors')) {
           $('#input_pn').removeClass('errors');
        }
      }

      if(!serial) {
        $('#input_sn').addClass('errors');
      } 
      else {
        if($('#input_sn').hasClass('errors')) {
           $('#input_sn').removeClass('errors');
        }
      } 

      if(!selectedIssue) {
        $('#selectIssue').addClass('errors');
      }
      else {
        if($('#selectIssue').hasClass('errors')) {
           $('#selectIssue').removeClass('errors');
        }
      }

     if(product && serial && selectedIssue && companyID) {

        $('.new-product').removeClass('errors');

        $('#table-product').append('<tr><td>'+ product +'</td><td>'+ serial +'</td><td>'+ selectedIssue +'</td></tr>');

        $('#submitRepair').removeClass('hide');

        if(count < 1) {
          
          has_product = true;

          newList[count]['product'] = product;
          newList[count]['serial'] = serial;
          newList[count]['issue'] = selectedIssue; 
        }
        else {

          var prodStore = [];

          prodStore['product'] = product;
          prodStore['serial'] = serial;
          prodStore['issue'] = selectedIssue; 


          newList.push(prodStore);
        }
        count++;
   

        $('#input_pn').val('');
        $('#input_sn').val('');
        $('#selectIssue').val('');
        
     }



  });


   var prep_prod = '';
 
  $('#wrap-form').on('click', '#submitRepair', function(e)
  {

      e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });


        var company      = $('#input_cn').val();
        var prodList     = $('#prodList');
        var user_id      = $('#searchCompany').val(); 
        var company_name = $('.company-list .single-company strong').text();

        if(!has_product) {
          $('.new-product').addClass('errors');
        }
        else {
          $('.new-product').removeClass('errors');
        }

        $('.loading').removeClass('hide');

        var p_name = '',
            p_serial = '',
            p_issue = '';
            notify_label = $('.form').find('.notification-success');

        for(var x= 0; x < newList.length; x++) {

              p_name = newList[x]['product'];
              p_serial = newList[x]['serial'];
              p_issue = newList[x]['issue'];


              $.ajax({
                type : 'get',
                url  : '{{URL::to('/storeData')}}',
                data: {
                   'company': company_name,
                   'user_id' : user_id,
                   'product' : p_name,
                   'serial' : p_serial,
                   'issue' : p_issue
                },
                success: function (data) {
                    //console.log(data);

                    if(data == 'saved') {
                      $('#table-product tbody').html("<tr><th>Product Name</th><th>Serial No</th><th>Issue</th></tr>");
                      $('.loading').addClass('hide');
                      $('#submitRepair').addClass('hide');

                      window.location.href  = '{{ url("/repairs/create") }}';

                    }
                     
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

        }


  });

 



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



</script>
@endsection


