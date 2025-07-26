@extends('layouts.app')

@section('content')

  
    <div class="panel panel-top">
      <div class="grid justify-space-between">
        <div class="col">
          {!! Breadcrumbs::render('softwares') !!} 
        </div>
        <div class="col text-right">
          <a href="{{ url('admin/softwares/create') }}"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-pencil btn-icon"></i><span>Create task</span></a>
        </div>
      </div>
    </div> 


  @include('softwares/resolve/search')
  @component('components/panel')
      @slot('title')
        Resolved Tasks
      @endslot
      
      @include('softwares/resolve/filter', ['totalitems' => $totalitems])
  
       @component('components/table')
            @slot('heading')
               <th width="80" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Task ID</th>
               <th width="400" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Summary</th>
               <th width="340" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Assigned To</th>
               <th style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Type</th>
               <th width="200" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Product</th>
               <th width="180" style="background: #f5f5f5;border-left: 1px solid #ddd;border-right: 1px solid #ddd;">Action</th>
            @endslot

           @if(!empty($tickets))

             @foreach($tickets as $ticket) 
                <tr class="single-task">
                  <td>{{ $ticket->id }}</td>
                  <td>
                    <strong>{{ $ticket->summary }}</strong>
                  </td>
                  <td>
                    <div class="task-owner">
                       <div class="img">
                          <?php $photo = DB::table('user_details')->where('user_id', $ticket->assigned_to )->value('photo'); ?>
                          @if( $photo )
                            <div class="photo">
                              <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="100%" />
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
                     </div>
                  </td>
                   <td>
                     {{ $ticket->product->name }}
                   </td>
                  <td>
                     <div class="task-option">
                       <ul class="list-inline">
                         <li><a href="{{ url('admin/softwares/'. $ticket->id) }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-eye"></i><span>View Task</span></a></li>
                       </ul>
                    </div>
                  </td>
               </tr>   
            @endforeach
          @else
              <tr><td colspan="4">No Data</td></tr>
          @endif  

      @endcomponent

      @if (isset($pagination))
      <nav aria-label="Page navigation example">
          <ul class="pagination" style="margin: 0;">
              @foreach ($pagination as $key => $link)
                  <li class="page-item">
                      @if ($key === 0)
                          <a class="page-link" href="{{ $link->url }}"><<</a>
                      @elseif (count($pagination) == $key + 1)
                          <a class="page-link" href="{{ $link->url }}">
                              >>
                          </a>
                      @else
                          <a class="page-link" 
                              style="{{ $link->active ? 'font-weight:bold;background:#3097d1;color:#fff;' : 'color:black;' }}" href="{{ $link->url }}"
                          >
                              {{ $link->label }}
                          </a>
                      @endif
                  </li>
              @endforeach
          </ul>
      </nav>
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
  select#totalItems {
    padding: 5px;
    border: 1px solid #ddd;
    width: 50px;
    height: 31px;  
  }
  input#search {
      height: 35px;
      margin-bottom: 5px;
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
  .task-status-view.is-wide {
      margin-top: 15px;
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
      background: rgba(37, 37, 37, 0.8);
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
  button#clearBtn span {
    width: 100%;
    margin: 0 auto;
    padding: 0 10px;
  }
</style>
@stop
@section('js')

    <script>
        $('#search').on('keyup', function(){

            var value     = $(this).val();
            var items     = $('#totalItems option:selected').val();
            var urlParams = new URLSearchParams(window.location.search);
            var link      = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserResolveTask') : URL::to('searchResolveTask') }}';

            // Set View
            if( urlParams.has('view') ) {
                var view = urlParams.get('view');
            } else {
                var view = 'list';
            }

            $.ajax({
                 type : 'get',
                 url  : link,
                 data: {
                  'search' : value,
                  'items' : items
                 },
                 success: function(data) {
                    console.log(data);
                    var data         = data['tasks'];
                    var total_items  = data['data'].length;
                    var output       = '';
                    var col1         = [];
                    var col2         = [];
                    var col3         = [];
                    var current_page = data['current_page'];
                    var last_page    = data['last_page'];
                    var items        = data['per_page'];
                    var from         = data['from'];
                    var total        = data['total'];
                    var paginate     = '';


                  

                    if(  view == 'list' ) {

                      if( total_items > 0 ) {
                          for(var x = 0; x < total_items; x++ ) {
                             output += listView(data['data'][x]);
                          } 
                      } else {
                          output = '<tr><td colspan="6">No data found</td></tr>';
                      }

                    
                    } else  if(  view == 'grid' ) {
                    
                      if( total_items > 0 ) {

                          for(var x = 0; x < total_items; x++ ) {

                               if(data['data'][x]['status'] === 'To Do') {

                                  col1 +=  gridView(data['data'][x]);
                                  
                               }
                               if(data['data'][x]['status'] === 'In Progress') {
                      
                                  col2 += gridView(data['data'][x]);
                               }
                               if(data['data'][x]['status'] === 'Completed') {

                                  col3 += gridView(data['data'][x]);
                               }

                          } 

                      } else {
                          output = 'No data found';
                      }

                    }


                    if(last_page > 1) {
                        paginate += '<ul class="pagination pagination-ajax">';

                        for(var a = 1; a <= last_page; a++) {

                              if( last_page > 1 && 1 == a  ) {
                                var is_disabled = ( current_page == 1 ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+from+'" '+ is_disabled +'>«</a></li>';
                              }

                              if( current_page === a ) {

                                paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                              } else {

                                paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                              }

                              if( last_page > 1 && last_page == a ) {
                                var is_disabled = ( current_page == last_page ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+last_page+'" '+ is_disabled +'>»</a></li>';
                              }
                        }
                        paginate += '</div>';
                    }

                    if(  view == 'list' ) {
                        $('tbody').html(output);
                        $('.pagination-links').html(paginate);
                    } else {
                       $('#grid-wrapper').find('.col-todo .inner-list').html(col1);
                       $('#grid-wrapper').find('.col-progress .inner-list').html(col2);
                       $('#grid-wrapper').find('.col-completed .inner-list').html(col3);
                       $('.pagination-links').html(paginate);
                    }
                 }
            });

       });

      $('.pagination-links').on('click', '.page-link', function(e){

          e.preventDefault();

          var urlParams = new URLSearchParams(window.location.search);

          // Set View
          if( urlParams.has('view') ) {
              var view = urlParams.get('view');
          } else {
              var view = 'list';
          }
          var page_id  = $(this).attr('id');
          var value = $('#search').val();
          var items = $('#totalItems option:selected').val();
          var output2 = '';
          var link = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserResolveTask') : URL::to('searchResolveTask') }}';
      
 

            $.ajax({
                 type : 'get',
                 url  : link,
                 data: {
                  'search' : value,
                  'items'  : items,
                  'view'   : view,
                  'page'   : page_id,

                },
                 success: function(data) {
                    console.log(data);

                    var data         = data['tasks'];
                    var total_items  = data['data'].length;
                    var output       = '';
                    var col1         = [];
                    var col2         = [];
                    var col3         = [];
                    var current_page = data['current_page'];
                    var last_page    = data['last_page'];
                    var items        = data['per_page'];
                    var from         = data['from'];
                    var total        = data['total'];
                    var paginate     = '';

                    if( total_items > 0 ) {

                      for(var x = 0; x < total_items; x++ ) {

                          if(  view == 'list' ) {
                              if( total_items > 0 ) {
                                  for(var x = 0; x < total_items; x++ ) {
                                     output += listView(data['data'][x]);
                                  } 
                              } else {
                                  output = '<tr><td colspan="6">No data found</td></tr>';
                              }
                          } else if(  view == 'grid' ) {
                            if( total_items > 0 ) {
                                for(var x = 0; x < total_items; x++ ) {
                                     if(data['data'][x]['status'] === 'To Do') {
                                        col1 +=  gridView(data['data'][x]);
                                     }
                                     if(data['data'][x]['status'] === 'In Progress') {
                                        col2 += gridView(data['data'][x]);
                                     }
                                     if(data['data'][x]['status'] === 'Completed') {
                                        col3 += gridView(data['data'][x]);
                                     }
                                } 
                            } else {
                                output = 'No data found';
                            }

                          }

                      }

                    } else {
                        output = '<tr><td colspan="6">No Data</td></tr>';
                    }

                      
                    
                    if(last_page > 1) {
                 
                        paginate += '<ul class="pagination pagination-ajax">';

                        for(var a = 1; a <= last_page; a++) {
                  

                              if( last_page > 1 && 1 == a  ) {
                                var is_disabled = ( current_page == 1 ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+from+'" '+ is_disabled +'>«</a></li>';
                              }

                              if( current_page === a ) {

                                paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                              } else {

                                paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                              }

                              if( last_page > 1 && last_page == a ) {
                                var is_disabled = ( current_page == last_page ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+last_page+'" '+ is_disabled +'>»</a></li>';
                              }
                            
                        }
                        paginate += '</div>';
                    }


                    if(  view == 'list' ) {
                        $('tbody').html(output);
                        $('.pagination-links').html(paginate);
                    } else {
                       $('#grid-wrapper').find('.col-todo .inner-list').html(col1);
                       $('#grid-wrapper').find('.col-progress .inner-list').html(col2);
                       $('#grid-wrapper').find('.col-completed .inner-list').html(col3);
                       $('.pagination-links').html(paginate);
                    }
                 }
            });

      });

      /**
       * [description]
       * @param  {String} 
       * @return {[type]}      [description]
       */
      $('#clearBtn').on('click', function(e){

              // remove input value
              $('#search').val('')

              var urlParams = new URLSearchParams(window.location.search);

              // Set View
              if( urlParams.has('view') ) {
                  var view = urlParams.get('view');
              } else {
                  var view = 'list';
              }

              var value = '';
              var items = $('#totalItems option:selected').val();
              var link = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserResolveTask') : URL::to('searchResolveTask') }}';
              
              $.ajax({
                   type : 'get',
                   url  : link,
                   data: {
                    'search' : value,
                    'items' : items,
                    'view' : view,
                    'show_all' : true 

                  },
                   success: function(data) {
                    console.log(data);
                    var data         = data['tasks'];
                    var total_items  = data['data'].length;
                    var output       = '';
                    var col1         = [];
                    var col2         = [];
                    var col3         = [];
                    var current_page = data['current_page'];
                    var last_page    = data['last_page'];
                    var items        = data['per_page'];
                    var from         = data['from'];
                    var total        = data['total'];
                    var paginate     = '';

                    if( total_items > 0 ) {

                      for(var x = 0; x < total_items; x++ ) {

                          if(  view == 'list' ) {
                              if( total_items > 0 ) {
                                  for(var x = 0; x < total_items; x++ ) {
                                     output += listView(data['data'][x]);
                                  } 
                              } else {
                                  output = '<tr><td colspan="6">No data found</td></tr>';
                              }
                          } else if(  view == 'grid' ) {
                            if( total_items > 0 ) {
                                for(var x = 0; x < total_items; x++ ) {
                                     if(data['data'][x]['status'] === 'To Do') {
                                        col1 +=  gridView(data['data'][x]);
                                     }
                                     if(data['data'][x]['status'] === 'In Progress') {
                                        col2 += gridView(data['data'][x]);
                                     }
                                     if(data['data'][x]['status'] === 'Completed') {
                                        col3 += gridView(data['data'][x]);
                                     }
                                } 
                            } else {
                                output = 'No data found';
                            }
                          }

                      }

                    } else {
                        output = '<tr><td colspan="5">No Data</td></tr>';
                    }


                    if(last_page > 1) {
                          paginate += '<ul class="pagination pagination-ajax">';

                          for(var a = 1; a <= last_page; a++) {

                              if( last_page > 1 && 1 == a  ) {
                                var is_disabled = ( current_page == 1 ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+from+'" '+ is_disabled +'>«</a></li>';
                              }

                              if( current_page === a ) {

                                paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                              } else {

                                paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                              }

                              if( last_page > 1 && last_page == a ) {
                                var is_disabled = ( current_page == last_page ) ? 'class="disabled"': '';
                                paginate += '<li><a href="javascript:void(0)" id="'+last_page+'" '+ is_disabled +'>»</a></li>';
                              }
                            

                          }
                          paginate += '</div>';
                      }

                    if(  view == 'list' ) {
                        $('tbody').html(output);
                        $('.pagination-links').html(paginate);
                    } else {
                       $('#grid-wrapper').find('.col-todo .inner-list').html(col1);
                       $('#grid-wrapper').find('.col-progress .inner-list').html(col2);
                       $('#grid-wrapper').find('.col-completed .inner-list').html(col3);
                       $('.pagination-links').html(paginate);
                    }
                   }
              });
       });

        function listView( ticket = [] ) {

             var type_color   = getTaskTypeColor( ticket['type'] );
             var status_color = getTaskStatusColor( ticket['status'] );
             var output       = '';
             var type         = ticket['type'];
             var status       = ticket['status'];
             var summary      = ticket['summary']
             var id           = ticket['id'];
             var created_at   = ticket['created_at'];
             var photo_link   = ticket['photo_link'];
             var fullname     = ticket['fullname'];
             var total_tasks  = ticket['total_tasks'];
             var url          = ticket['link'];
             var product      = ticket['product'];

                output += '<tr class="single-task">';
                    output += '<td>'+ id + '</td>';
                    output += '<td>';
                     output += '<strong>'+ summary + '</strong>';
                    output += '</td>';
                    output += '<td>';
                        output += '<div class="task-owner">';
                            output += '<div class="img">';
                                output += '<div class="photo">';
                                 output += '<img src="'+ photo_link +'" width="100%" />';
                                output += '</div>';
                            output += '</div>';
                            output += '<div class="info">';
                              output += '<strong>'+ fullname +'</strong>';
                              output += '<span>Total Tasks : <span>'+ total_tasks + '</span></span>';
                            output += '</div>';
                        output += '</div>';    
                    output += '</td>';
                    output += '<td>'; 
                        output += '<div class="task-info">';
                            output += '<p class="'+ type_color +'"><strong>'+ type +'</strong></p>';
                        output += '</div>';
                    output += '</td>';  
                    output += '<td>'; 
                        output += '<div class="task-product">';
                         output += '<span>'+ product +'</span>';
                        output += '</div>';
                    output += '</td>';
                    // output += '<td>'; 
                    //     output += '<div class="task-status">';
                    //      output += '<span class="'+ status_color + ' box-bg">'+ status +'</span>';
                    //     output += '</div>';
                    // output += '</td>';  
                    output += '<td>'; 
                        output += '<div class="task-option">';
                          output += ' <ul class="list-inline">';
                          output += '<li><a href="'+ url + '" class="btn-brand btn-brand-icon btn-brand-primary" target="_blank"><i class="fa fa-eye"></i><span>View Task</span></a></li>';
                          output += '</ul>';
                        output += '</div>';
                    output += '</td>';
                  output += '</tr>';
    
            return output;
       }

      /**
       * [listView description]
       * @param  {Array}  ticket [description]
       * @return {[type]}        [description]
       */
      function gridView( ticket = [], task = [] ) {

         var type_color   = getTaskTypeColor( ticket['type'] );
         var status_color = getTaskStatusColor( ticket['status'] );
         var output       = '';
         var type         = ticket['type'];
         var status       = ticket['status'];
         var summary      = ticket['summary']
         var id           = ticket['id'];
         var created_at   = ticket['created_at'];
         var photo_link   = ticket['photo_link'];
         var fullname     = ticket['fullname'];
         var url          = ticket['link'];

    
          task += '<a href="'+ url +'" id="'+ id +'">';
            task += '<div class="single-status">';
              task += '<div class="col-left">';
                  task += '<strong>Task #'+ id +'</strong>';
                  task += '<p>'+ summary +'</p>';
              task += '</div>';
              task += '<div class="col-right">';
                task += '<div class="photo">';
                  task += '<img src="'+ photo_link +'"  width="100%" />';
                task += '</div>';
              task += '</div>';
            task += '</div>';  
          task += '</a>';
       

          return task;
     }

      function getTaskTypeColor( task_type )
      {
          var color = '';
          switch( task_type )
          {
                  case 'Feature':
                    color = 'text-color-1';
                    break;
                  case 'Request':
                    color = 'text-color-2';
                    break;
                  case 'Defect':
                    color = 'text-color-3';
                    break;
                  default:
                    color = 'text-color-1';
                    break;
          }

            return color;  
      }

      function getTaskStatusColor( task_status )
      {
          var color = '';
          switch( task_status )
              {
                case 'To Do':
                   color = 'bg-color-1';
                   break;

                case 'In Progress':
                   color = 'bg-color-2';
                   break;
                   
                case 'Completed':
                   color = 'bg-color-3';
                   break; 
                default:
                   color = '';
                   break;
              }

            return color;  
      }

      function addViewForm() {

            var urlParams = new URLSearchParams(window.location.search);

            // Set View
            if( urlParams.has('view') ) {
                var view = urlParams.get('view');
            } else {
                var view = 'list';
            }

            $('#inputView').val(view);

      }
      addViewForm();

       /**
        * [showTotalResults description]
        * @return {[type]} [description]
        */
      function showTotalResults() 
      {
         $('#totalItems').on('change', function(){
            var totalItems = $(this).prop('selected', true).val();
            var url        = '{{ url('admin/resolved-issues') }}';

            var urlParams = new URLSearchParams(window.location.search);

            // Set View
            if( urlParams.has('view') ) {
                var view = urlParams.get('view');
            } else {
                var view = 'list';
            }


            window.location.replace(url + '?' + 'items=' + totalItems + '&view=' + view );
            

         }); 

       }

       /**
        * [toggleSearch description]
        * @return {[type]} [description]
        */
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

      /**
       * [statusColor description]
       * @param  {[type]} status [description]
       * @return {[type]}        [description]
       */
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


       /**
        * [advancedSearch description]
        * @return {[type]} [description]
        */
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


       /**
        * Close Task holder
        * @param  {[type]}    [description]
        * @return {[type]}     [description]
        */
       $('#closeTaskHolder').on('click', function(){
            $(this).closest('.task-holder').addClass('is-hide');
            $('.task-status-view').addClass('is-wide');
       });

       /**
        * Open Status View
        * @param  {[type]} 
        * @return {[type]}      [description]
        */
       $('.task-status-view').on('click', 'a', function(e){

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

