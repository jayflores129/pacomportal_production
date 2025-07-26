@extends('layouts.app')

@section('content')


  <div class="panel panel-top">
    <div class="grid justify-space-between">
      <div class="col">
        {!! Breadcrumbs::render('onlySubmittedTask') !!} 
      </div>
      <div class="col text-right">
        <a href="{{ url('admin/softwares/create') }}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil btn-icon"></i><span>Create Task</span></a>
      </div>
    </div>
  </div> 

  @include('softwares/view/filter')

  @component('components/panel')
        @slot('title')
          Tasks
        @endslot
          @if($view === 'grid') 
                 <?php   
                    $todos = array();
                    $progress = array();
                    $completed = array();
                  
                  foreach( $tickets as $ticket ) {
                     if ( $ticket->status == 'To Do' )  {
                         array_push( $todos, $ticket );
                     } elseif ( $ticket->status == 'In Progress' ) {
                         array_push( $progress, $ticket );
                      } elseif ( $ticket->status == 'Completed' ) {
                        array_push( $completed, $ticket );
                      }
                  } 

                ?>
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
              @include('softwares.view.table', ['tickets', $tickets])
        @endif
        <div class="pagination-links">
          {{ $tickets->links() }}
        </div>
  @endcomponent  

@endsection


@section('css')
<style>

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

