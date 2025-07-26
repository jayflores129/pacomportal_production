@extends('layouts.app')

@section('content')


    <div class="panel panel-top">
      <div class="row">
        <div class="col-sm-6">
          {!! Breadcrumbs::render('softwares') !!} 
        </div>
        <div class="col-sm-6 text-right">
          <a href="{{ url('admin/softwares/create') }}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil btn-icon"></i><span>Create Task</span></a>
        </div>
      </div>
    </div> 

  
      @include('softwares/view/filter')
      @component('components/panel')
        @slot('title')
          Tasks
        @endslot
          @include('softwares/view/search')
          @if($view === 'grid') 
                @if( Auth::user()->isAdmin() )
                  <div class="row margin-bm">
                    <div class="col-sm-4">
                      <label for="search" class="hide">Quick Search</label>
                      <span><input type="text" id="search" name="search" class="form-control" placeholder="Quick Search.."></span>
                    </div>
                    <div class="col-sm-4"> 
                        <ul class="list-inline">
                          <li><button id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger"><span>Clear Search</span></button></li>
                          <li><a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                     Advanced Search</span></a></li>
                        </ul>  
                     
                    </div> 
                  </div>
                @endif
                <!-- Grid View -->
                <div class="grid">
                  <div class="status-grid-view task-status-view is-wide">
                      <div class="col">
                        @include('softwares.view.task', ['title' => 'To Do', 'color' => 'text-color-1', 'lists' => $todos])
                      </div>  
                      <div class="col">
                          @include('softwares.view.task', ['title' => 'In Progress', 'color' => 'text-color-3', 'lists' => $progress])
                      </div>  
                      <div class="col">
                          @include('softwares.view.task', ['title' => 'Completed', 'color' => 'text-color-2', 'lists' => $completed])
                      </div>  
                  </div>
                  @include('softwares.view.task-preview')
                </div>      
        @else
            <div class="table-nav row">
              <div class="col-sm-9">
                <div class="row margin-bm">
                  <div class="col-sm-8">
                    <label for="search" class="hide">Quick Search</label>
                    <span><input type="text" id="search" name="search" class="form-control" placeholder="Quick Search.."></span>
                  </div>
                  <div class="col-sm-4"> 
                      <ul class="list-inline">
                        <li><button id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger"><span>Clear Search</span></button></li>
                        <li><a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                   Advanced Search</span></a></li>
                      </ul>  
                  </div> 
                </div>
            </div>
              <div class="col-sm-3">
                  <div class="float-right">
                    <span>Show Results 
                      <?php $items = [ 15,25, 50, 100 ]; ?>
                      @if($items)
                        <select id="totalItems">
                           @foreach($items as $item)
                              <option value="{{ $item }}" <?php echo ( $totalitems == $item ) ? 'selected="selected"' : ''; ?>>{{ $item }}</option> 
                           @endforeach 
                        </select>
                      @endif
                    </span>
                </div>
              </div>
            </div>

              @include('softwares.view.table', ['tickets', $tickets])
        @endif
      @endcomponent    


@endsection

@section('css')
<style>
  .margin-bm {
    margin-bottom: 20px;
  }

  #clearBtn span {
    width: 100%;
  }
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
  .table-nav {
    margin-bottom: 10px;
  }
  .advanced-search:focus {
    outline: none;
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
  .repair-tab.top-search {
    background: rgba(60, 58, 58, 0.7);
    border-radius: 0;
    border: 0;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
  }
  .repair-tab.top-search  .panel-body {
    max-width: 500px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 100;
    background: #fff;
    box-shadow: 0 9px 17px #4e4c4c;
    padding: 40px 20px;
  }
  #closeAdvanced {
    position: absolute;
    top: 0;
    right: 0;
    background: transparent;
    border: 0;
    background: #fff;
    border-radius: 50%;
    padding: 10px;
  }
  #closeAdvanced:focus {
    outline: none;
  }
  #clearBtn {
    margin-bottom: 5px;
    width: 120px;
    padding: 0 5px;
  }
  #clearBtn span {
    width: 100%;
  }
</style>
@stop
@section('js')

    <script>

       $('#search').on('keyup', function(){

            var value = $(this).val();
            var items = $('#totalItems option:selected').val();
            

            $.ajax({
                 type : 'get',
                 url  : '{{URL::to('searchTask')}}',
                 data: {
                  'search' : value,
                  'items' : items,

                },
                 success: function(data) {
                  console.log(data);

                  var data = data;
                  var total = data['total'];
                  var output = '';


                    output = data['output'];

                    var itemsLeft = '';

                    if(total % items) {
                      itemsLeft = Math.ceil( total / items) ;
                    } else {
                      itemsLeft = total / items;
                    }

                    console.log("pages : " + itemsLeft);
                    console.log('items per page :' + items);

                    var link = '<a href="#" class="btn-brand btn-next" id="'+ itemsLeft  +'">Next Page</a>';

                    var paginate = '';
                    if(itemsLeft > 0) {
                        paginate += '<ul class="pagination pagination-ajax">';

                        for(var a = 1; a <= itemsLeft; a++) {

                            if( 1 === a ) {

                              paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                            } else {

                              paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                            }

                        }
                        paginate += '</div>';
                    }


                    $('tbody').html(output);
                    $('.pagination-links').html('');
                 }
            });

       });

       $('.pagination-links').on('click', '.page-link', function(e){

           e.preventDefault();

           

          var page_id  = $(this).attr('id');
          var value = $('#search').val();
          var items = $('#totalItems option:selected').val();
          var output2 = '';
          console.log(page_id);
            $.ajax({
                 type : 'get',
                 url  : '{{URL::to('searchTask')}}',
                 data: {
                  'search' : value,
                  'items' : items,
                  'page' : page_id,

                },
                 success: function(data) {
                  console.log(data);

                   var data = data;
                   var total = data['total'];
                   var start_page   = parseInt(items)  + 1;  
                   var end_page = items * page_id;
                   start_page = ( end_page - parseInt(items)   ) + 1;

                    if( total < end_page ) {
                      end_page = total;
                    }

                    if(page_id == 1)  {
                      start_page = 0;
                      end_page = items;
                    }

                    output2 = data['output'];

                    var itemsLeft = '';

                    if(total % items) {
                      itemsLeft = Math.ceil( total / items) ;
                    } else {
                      itemsLeft = total / items;
                    }

                    console.log("pages : " + itemsLeft);
                    console.log('items per page :' + items);

                    //var link = '<a href="#" class="btn-brand btn-next" id="'+ itemsLeft  +'">Next Page</a>';

                    var paginate = '';
                    if(itemsLeft > 0) {
                        $('tbody').html('');
                        paginate += '<ul class="pagination pagination-ajax">';

                        for(var a = 1; a <= itemsLeft; a++) {

                            if( page_id == a ) {

                              paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                            } else {

                              paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                            }
                            
                        }
                        paginate += '</div>';
                    }



                    $('tbody').html(output2);
                    $('.pagination-links').html(paginate);
                 }
            });

       });

       $('#clearBtn').on('click', function(e){

            // remove input value
            $('#search').val('')

            var value = '';
            var items = $('#totalItems option:selected').val();
            
            $.ajax({
                 type : 'get',
                 url  : '{{URL::to('searchTask')}}',
                 data: {
                  'search' : value,
                  'items' : items,
                  'list' : true

                },
                 success: function(data) {
                  console.log(data);

                  var data = data;
                  var total = data['total']
                  var output = '';

                    output = data['output'];
                    var itemsLeft = '';

                    if(total % items) {
                      itemsLeft = Math.ceil( total / items) ;
                    } else {
                      itemsLeft = total / items;
                    }

                    console.log("pages : " + itemsLeft);
                    console.log('items per page :' + items);

                    var link = '<a href="#" class="btn-brand btn-next" id="'+ itemsLeft  +'">Next Page</a>';

                    var paginate = '';
                    if(itemsLeft > 0) {
                        paginate += '<ul class="pagination pagination-ajax">';

                        for(var a = 1; a <= itemsLeft; a++) {

                            if( 1 === a ) {

                              paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                            } else {

                              paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                            }

                        }
                        paginate += '</div>';
                    }


                    $('tbody').html(output);
                    $('.pagination-links').html('');
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


            $('.advanced-search').removeClass('hide').addClass('show');

          });
          $('#closeAdvanced').on('click', function(){
              $('.advanced-search').removeClass('show').addClass('hide');
          });
 
       }
      function statusColor(status) 
      {
       
           switch (status) {
               case "open":
                 color = "btn-default btn-open";
                 break;

              case "Partially Shipped":
                 color = "btn-default btn-ps";
                 break;
                 
              case "Completely Shipped":
                 color = "btn-default btn-cs";
                 break; 

                case "received":
                 color = "btn-default btn-r";
                 break;
                 
                case "repaired":
                 color = "btn-default btn-rp";
                 break;  

                case "returned":
                 color = "btn-default btn-rt";
                 break;  
                case "shipped":
                 color = "btn-default btn-rt";
                 break;  
               
               default:
                 color = "";
                 break;
            }

            return color;
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



       $('#closeTaskHolder').on('click', function(){
            $(this).closest('.task-holder').addClass('is-hide');
            $('.task-status-view').addClass('is-wide');
       });

       $('.task-status-view a').on('click', function(e){

           e.preventDefault();

           var id = $(this).attr('id');

            $.ajax({
                type: 'get',
                url:  '{{ url('/show-info') }}',
                data: {
                  id: id,
                },
                success : function(data) {
                    

                  $('.task-holder .task-summary p').text( data['ticket']['summary'] );
                  $('.task-holder .task-dates ul li:first-child .value').text( data['ticket']['created_at'] );
                  $('.task-holder .task-dates ul li:last-child .value').text( data['ticket']['updated_at'] );
                  $('.task-holder .task-description .value').text( data['ticket']['description'] );
                  $('.task-holder .task-heading strong').text( 'Task ID #' + data['ticket']['id'] );

                  $('.task-holder .task-info ul li:first-child .value').html( data['ticket']['status'] + ' <a href="' + data['link'] + '" target="_blank">( View Workflow )</a>' );


                  $('.task-holder .task-in-charge ul li:first-child .value').text( data['submitter'] );
                  $('.task-holder .task-in-charge ul li:last-child .value').text( data['assigned'] );

                  $('.task-holder').removeClass('is-hide');
                  $('.task-status-view').removeClass('is-wide');


                },
                eror : function(error) {
                    console.log(error);
                }

            }) 

            console.log(id );
       });
    </script>
@stop
