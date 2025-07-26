@extends('layouts.app')

@section('content')

  @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
    <div class="panel panel-top">
      <div class="row">
        <div class="col-sm-6">
          {!! Breadcrumbs::render('allOpenRepairs') !!} 
        </div>
        <div class="col-sm-6 text-right">
          <a href="{{ url('repairs/create') }}"   class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-eye btn-icon"></i><span>Create Repair</span></a>
        </div>
      </div>
    </div> 
  @endif
 <a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-filter"></i> <span>Advanced Search</span></a>
  <div class="advanced-search">
      <div class="panel panel-default repair-tab top-search">
      {{--  <div class="panel-header">
                 <h3 class="heading">Advanced Search</h3>
              </div> --}}
        <div class="panel-body">
          @if($repairs)
              <div class="row">
                <div class="col-sm-4">
                  <div class="filter">
                    <label for="companyName">Company</label>
                    <input type="text" id="companyName" placeholder="Company Name" />
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="filter">
                    <label for="repairProduct">Product</label>
                    @if($products)
                       <select id="repairProduct">
                         <option value="">Select Product</option>
                         @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                           @endforeach
                        </select>
                      @endif
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="filter">
                    <label for="repairStatus">Status</label>
                    <select id="repairStatus">
                        <option value="">Select Status</option>
                        <option value="open">Open</option>
                        <option value="Partially Shipped">Partially Shipped</option>
                        <option value="Completely Shipped">Completely Shipped</option>
                        <option value="received">Received</option>
                        <option value="repaired">Repaired</option>
                        <option value="returned">Returned</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="filter">
                      <button type="submit" id="advancedSearch"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check"></i> <span>Submit</span></button>
                  </div>
                </div>
              </div>
              
          @endif
        </div>

  </div>  
  </div>
  <div class="row">
    <div class="col-sm-12">
    
        <div class="panel panel-default panel-brand">
          <div class="panel-heading">
            <h3>All Open Repairs </h3>
          </div>
          <div class="panel-body">
            <div class="table-nav row">
              <div class="col-sm-4">
                  {{-- <label for="search" class="hide">Quick Search</label>
                  <span><input type="text" id="search" name="search" class="form-control" placeholder="Quick Search.."></span> --}}
              </div>
            </div>
                <div class="table-responsive"> 
                 <table class="table table-striped">
                    <thead>
                    <tr class="visible-xs">
                        <th>Details</th>
                    </tr>
                     <tr class="hidden-xs">
                         <th>RMA Number</th>
                         <th>Company</th>
                         <th>Product</th>
                         <th>Issue</th>
                         <th>Status</th>
                         <th>Under Warranty</th>
                         <th>Date Added</th>
                         <th>Action</th>
                       
                     </tr>  
                    </thead>
                    <tbody>
                    @if($repairs)

                     @foreach ($repairs as $repair )

                     <?php if($repair->under_warranty) {
                         $is_w = 'Yes' ;
                     }
                     else {
                         $is_w = '';
                     }
                     $class = '';

                     switch ($repair->status) {
                       case 'open':
                         $class = 'btn-default btn-open';
                         break;

                      case 'Partially Shipped':
                         $class = 'btn-default btn-ps';
                         break;
                         
                      case 'Completely Shipped':
                         $class = 'btn-default btn-cs';
                         break; 

                        case 'received':
                         $class = 'btn-default btn-r';
                         break;
                         
                        case 'repaired':
                         $class = 'btn-default btn-rp';
                         break;  

                        case 'returned':
                         $class = 'btn-default btn-rt';
                         break;  
                       
                       default:
                         $class = '';
                         break;
                     }
                     ?> 
                   

                     <tr class="hidden-xs">
                         <td>{{ $repair->rma_no }}</td>
                         <td>{{ $repair->company }}</td>
                         <td>{{ $repair->product  }}</td>

                         <td>{{ $repair->issue }}</td>
                         <td><span class="{{ $class }}">{{ $repair->status }}</span></td>
                         <td>{{ $is_w }}</td>
                         <td>{{ date('F d, Y', strtotime($repair->created_at))  }}</td>
                         <td>
                            <a href="{{ route('repairs.show', $repair->id)}}">Show Info</a>
                         </td> 
                     </tr>
                     <tr class="visible-xs">
                            <td>
                                    <div class="info">
                                           <p><strong>RMA No</strong> : {{ $repair->rma_no }}</p>
                                           <p><strong>Company</strong> : {{ $repair->company }}</p>
                                           <p><strong>Product</strong> : {{ $repair->product  }}</p>
                                           <p><strong>Product Serial No</strong> : {{ $repair->product_serial_no  }}</p>
                                           <p><strong>Issue</strong>  : {{ $repair->issue }}</p>
                                           <p><strong>Status</strong> : {{ $repair->status }}</p>
                                           <p><strong>Under Warranty</strong>: {{ $is_w }}</p>
                                           <p><strong>Date Added</strong> : {{ date('F d, Y', strtotime($repair->created_at))  }}</p>

                                           @if(Auth::user()->hasRole(['admin', 'SPG Internal User', 'super admin']))
                                           <p>

                                                {!! Form::open([
                                                     'method' => 'delete',
                                                     'route' => ['repairs.destroy', $repair->id]
                                                    ]) !!}
                                                   <a href="{{ route('repairs.edit', $repair->id)}}"><span class="fa fa-pencil"></span></a>


                                                     <button type="submit"><span class="fa fa-trash"></span></button>


                                                   {!! Form::close() !!}
                                           </p>
                                           @elseif(Auth::user()->hasRole('customer'))
                                               <a href="{{ route('repairs.show', $repair->id)}}"><span class="fa fa-eye"></span></a>
                                           @endif
                                    </div>
                              </td>      
                         </tr>

                        @endforeach
                      @endif  

                   </tbody>
                 </table>
                </div>  
                 @if($repairs)
                 <div class="pagination-links">
                    {{ $repairs->links() }} 
                 </div>   
                 @endif   

              
          </div>
      </div>    

    </div>
  </div>


@endsection

@section('css')
<style>
  .repair-tab {
    min-height: auto;
  }
  .repair-tab .panel-header {
      margin: 0;
      padding: 10px 20px;
      background: #2680d0;
  }
  .repair-tab .panel-header .heading {
      font-size: 1.2em;
      background: #2680d0;
      border: 2px solid #2680d0;
      color: #fff;
      margin: 0;
  }
  .repair-tab.top-search {
      background: #fff;
      border-radius: 0;
      border: 0;
  }
  select#totalItems {
    padding: 5px;
    border: 1px solid #ddd;
    width: 50px;
    height: 31px;  
  }
  input#search {
      height: 35px;
  }
  #advancedSearch {
      margin-top: 29px;
      width: 100%;
      height: 44px;
  }
   #advancedSearch i {
     height: 45px;
     line-height: 28px;
   }
   #advancedSearch span {
      font-size: 17px;
      height: 45px;
      line-height: 45px;
   }
  .btn-default {
    background: #f5f5f5;
    padding: 5px;
    line-height: 1;
    border-radius: 4px;
  }
  .btn-open {
      background: #fb9210;
      color: #000;
  }
  .btn-ps {
    background: #004181;
    color: #fff;
  }
  .btn-cs {
      background: #2ba01c;
      color: #fff;
  }
  .btn-r {
      background: #dc3030;
      color: #fff;
  }
  .btn-rp {
      background: #ebef1a;
      color: #000;
  }
  .btn-rt {
      background: #ae68d2;
      color: #fff;
  } 
  .table-nav {
    margin-bottom: 10px;
  }
  .advanced-search label {
    display: block;
  }
  .advanced-search input,
  .advanced-search select {
     margin: 5px 0 10px 0;
  }
  .advanced-search .panel-body {
      padding: 10px 15px;
  }
</style>
@stop
@section('js')

    <script>
       var status = '',
           product = '',
           value = '',
           company = '';

       //Product Change
       $('#repairProduct').on('change', function(){
           product = $(this).find('option:selected').text();

           if(product == 'Select') {
              product = '';
           }
       }); 

       //Repair Status Change
       $('#repairStatus').on('change', function(){
           status = $(this).prop('selected', true).val();

       }); 

       $('#searchSubmit').on('click', function(e) {

          e.preventDefault();

          $('.pagination').hide();
          
          company = $('#companyName').val();

           console.log(status);
            console.log(product);

            //SearchController
            $.ajax({
                 type : 'get',
                 url  : '{{URL::to('searchRepair')}}',
                 data: {
                  'search' : '',
                  'status' : status,
                  'product' : product,
                  'company' : company

                },
                 success: function(data) {
                  //console.log(data);

                    $('tbody').html(data);
                 }
            });


       });


       $('#search').on('keyup', function(){

            var value = $(this).val();
            
            if(value.length !== 0 ){
                $('.pagination').hide();
            }
            else  {

              $('.pagination').show();
            }
            //SearchController
            $.ajax({
                 type : 'get',
                 url  : '{{URL::to('searchRepair')}}',
                 data: {
                  'search' : value

                },
                 success: function(data) {
                  //console.log(data);

                    $('tbody').html(data);
                 }
            });

       });

       function toggleSearch() 
       {
          $('#advanced-search-button').on('click', function(e){

            e.preventDefault();

            $('.advanced-search').slideToggle();

          });
         
            
       }
        function advancedSearch()
       {
        

          $('#advancedSearch').on('click', function(e){

            e.preventDefault();

            var main_url = '<?php echo url('repairs') ?>';
            var url = '<?php echo url('search/repairs') ?>';
            var param = '';
            var company = $('#companyName').val();

            if(company) {
                param = 'company=' + company;
            }
            if(product) {
                param = 'product=' + product;
            }
            if(status) {
               param = 'status=' + status;
            }
            //product
            if(product && status && !company) {
              param = 'status=' + status + '&product=' + product;
            }
            if(product && !status && company) {
              param = '&product=' + product + '&company=' + company;
            }
            //status 
            if(!product && status && company) {
              param = 'status=' + status + '&company=' + company;
            }

            if(product && status && company) {
              param = 'status=' + status + '&product=' + product + '&company=' + company;
            }
          
            if( product || status || company ) {
              window.location.replace(url + '?' + param);
            } else {
              window.location.replace(main_url);
            }
           

            console.log(url);


          });

       }
       toggleSearch();
       advancedSearch();

    </script>
@stop

