@extends('layouts.app')

@section('content')

  @if(Auth::user()->hasRole(['', 'super ', 'SPG Internal User']))
    <div class="panel panel-top">
      <div class="grid justify-space-between">
        <div class="col">
          {!! Breadcrumbs::render('softwares') !!} 
        </div>
        <div class="col text-right">
          <a href="{{ url('/admin/softwares/create') }}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil btn-icon"></i><span>Create Task</span></a>
        </div>
      </div>
    </div> 
  @endif

  @include('softwares/view/filter')


        @component('components/panel')
            @slot('title')
              Tasks
            @endslot
            
             @component('components/table')
                  @slot('heading')
                     <th width="240">Assigned To</th>
                     <th>Type</th>
                     <th width="200">Status</th>
                     <th width="150">Action</th>
                  @endslot
    
                 @if(!empty($tickets))

                   @foreach($tickets as $ticket) 
                      <tr class="single-task">
                        <td>
                          <div class="task-owner">
                             <div class="img">
                                <?php $photo = DB::table('user_details')->where('user_id', $ticket->assigned_to )->value('photo'); ?>
                                @if( $photo )
                                  <div class="photo">
                                    <img src="{{ asset('images/uploads/' . $photo ) }}"  width="100%" />
                                  </div>
                                @else
                                  <div class="photo">
                                    <img src="{{ asset('/public/images//user-placeholder.png') }}" width="100%" />
                                  </div>
                                @endif


                             </div>
                             <div class="info">
                               <strong>{{ $ticket->assign->firstname .' '. $ticket->assign->lastname }}</strong>
                               <span>Total Tasks : <span>{{ DB::table('software_tickets')->where('assigned_to', $ticket->assign->id )->count() }}</span></span>
                             </div>
                         </div>
                        </td>
                        <td>
                           <div class="task-info">
                              <?php

                                  switch($ticket->type)
                                  {
                                    case 'Feature':
                                      $class = 'text-color-1';
                                      break;
                                    case 'Request':
                                      $class = 'text-color-2';
                                      break;
                                    case 'Defect':
                                      $class = 'text-color-3';
                                      break;
                                    default:
                                      $class = 'text-color-1';
                                      break;

                                  }

                              ?>
                              <p class="{{ $class }}"><strong>{{ $ticket->type }}</strong></p>
                              <span>{{ $ticket->summary }}</span>
                           </div>
                        </td>
                        <td>
                           <div class="task-status">
                             <?php
                                  switch ($ticket->status) {
                                     case 'To Do':
                                       $class = 'bg-color-1';
                                       break;

                                    case 'In Progress':
                                       $class = 'bg-color-2';
                                       break;
                                       
                                    case 'Completed':
                                       $class = 'bg-color-3';
                                       break; 
                                    default:
                                       $class = '';
                                       break;
                                 }
                              ?>
                              <span class="{{ $class }} box-bg">{{ $ticket->status }}</span>
                           </div>
                        </td>
                        <td>
                           <div class="task-option">
                             <ul class="list-inline">
                               <li><a href="{{ url('/softwares/'. $ticket->id) }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-eye"></i><span>View Task</span></a></li>
                             </ul>
                          </div>
                        </td>
                     </tr>   
                  @endforeach
                @else
                    <tr><td colspan="4">No Data</td></tr>
                @endif  

            @endcomponent

            <div class="pagination">
              {{ $tickets->links() }}
            </div>
        @endcomponent


@endsection

@section('css')
<style>
  .view-controls  a {
    color: #9e9e9e;
  }
  .view-controls  a.active {
    color: #1d6fb8;
  }
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
  .photo {
    border-radius: 50%;
    overflow: hidden;
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

      function showTotalResults() 
      {
         $('#totalItems').on('change', function(){
            var totalItems = $(this).prop('selected', true).val();
            var url        = '<?php echo url('repairs') ?>';


           
          window.location.replace(url + '?' + 'items=' + totalItems );
            

         }); 



       }

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
            // if(totalItems) {
            //   param = 'items=' + totalItems;
            // }

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
            // if(product && status && company && totalItems) {
            //   param = 'status=' + status + '&product=' + product + '&company=' + company + '&items=' + $totalitems;
            // }
            
            if( product || status || company  ) {
              window.location.replace(url + '?' + param);
            } else {
              window.location.replace(main_url);
            }
           
          });

       }
       showTotalResults();
       toggleSearch();
       advancedSearch();

    </script>
@stop

