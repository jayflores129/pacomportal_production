@extends('layouts.app')

@section('content')


    <div class="panel panel-top">
      <div class="grid justify-space-between">
        <div class="col">
          {!! Breadcrumbs::render('softwares') !!} 
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
          @include('softwares/view/search')
          @if($view === 'grid') 

            <div class="grid justify-space-between grid-search-wrapper">
              <div class="col">
                <form class="grid grid-search-wrapper">
                  <div class="col">
                    <label for="search" class="hide">Quick Search</label>
                    <span><input type="text" id="search" value="{{ request('search') }}" name="search" class="form-control" placeholder="Quick Search.."></span>
                  </div>
                  <div class="col"> 
                      <ul class="list-inline">
                        <li><button id="clearBtn" type="submit" class="btn-brand btn-brand-icon btn-brand-danger"><span>Search</span></button></li>
                        <li><a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                   Advanced Search</span></a></li>
                      </ul>  
                  </div> 
                </form>
            </div>
              <div class="col">
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

                <!-- Grid View -->
                <div class="grid">
                  <div id="grid-wrapper" class="status-grid-view task-status-view is-wide">
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
                    
                      <div class="col col-todo">
                        @include('softwares.view.task', ['title' => 'To Do', 'color' => 'text-color-1', 'lists' => $todos])
                      </div>  
                      <div class="col col-progress">
                          @include('softwares.view.task', ['title' => 'In Progress', 'color' => 'text-color-3', 'lists' => $progress])
                      </div>  
                      <div class="col col-completed">
                          @include('softwares.view.task', ['title' => 'Completed', 'color' => 'text-color-2', 'lists' => $completed])
                      </div>  
                  </div>
                  @include('softwares.view.task-preview')
                </div>      
        @else
            <div class="grid justify-space-between grid-search-wrapper">
              <div class="col">
                <form class="grid grid-search-wrapper">
                  <div class="col">
                    <label for="search" class="hide">Quick Search</label>
                    <span><input type="text" id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search.."></span>
                  </div>
                  <div class="col"> 
                      <ul class="list-inline">
                        <li><button type="submit" id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger"><span>Search</span></button></li>
                        <li><a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                   Advanced Search</span></a></li>
                      </ul>  
                  </div> 
                </form>
            </div>
              <div class="col">
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
        <div class="pagination-links">
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
        </div>

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
      height: 32px;
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
</style>
@stop
@section('js')
    <script>

       $('#search').on('keyup', function(){
            return;
            var value     = $(this).val();
            var items     = $('#totalItems option:selected').val();
            var urlParams = new URLSearchParams(window.location.search);
            var link      = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserTask') : URL::to('searchTask') }}';

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

    //   $('.pagination-links').on('click', '.page-link', function(e){

    //       e.preventDefault();

    //       var urlParams = new URLSearchParams(window.location.search);

    //       // Set View
    //       if( urlParams.has('view') ) {
    //           var view = urlParams.get('view');
    //       } else {
    //           var view = 'list';
    //       }
    //       var page_id  = $(this).attr('id');
    //       var value = $('#search').val();
    //       var items = $('#totalItems option:selected').val();
    //       var output2 = '';
    //       var link = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserTask') : URL::to('searchTask') }}';
      
 

    //         $.ajax({
    //              type : 'get',
    //              url  : link,
    //              data: {
    //               'search' : value,
    //               'items'  : items,
    //               'view'   : view,
    //               'page'   : page_id,

    //             },
    //              success: function(data) {
    //                 console.log(data);

    //                 var data         = data['tasks'];
    //                 var total_items  = data['data'].length;
    //                 var output       = '';
    //                 var col1         = [];
    //                 var col2         = [];
    //                 var col3         = [];
    //                 var current_page = data['current_page'];
    //                 var last_page    = data['last_page'];
    //                 var items        = data['per_page'];
    //                 var from         = data['from'];
    //                 var total        = data['total'];
    //                 var paginate     = '';

    //                 if( total_items > 0 ) {

    //                   for(var x = 0; x < total_items; x++ ) {

    //                       if(  view == 'list' ) {
    //                           if( total_items > 0 ) {
    //                               for(var x = 0; x < total_items; x++ ) {
    //                                  output += listView(data['data'][x]);
    //                               } 
    //                           } else {
    //                               output = '<tr><td colspan="6">No data found</td></tr>';
    //                           }
    //                       } else if(  view == 'grid' ) {
    //                         if( total_items > 0 ) {
    //                             for(var x = 0; x < total_items; x++ ) {
    //                                  if(data['data'][x]['status'] === 'To Do') {
    //                                     col1 +=  gridView(data['data'][x]);
    //                                  }
    //                                  if(data['data'][x]['status'] === 'In Progress') {
    //                                     col2 += gridView(data['data'][x]);
    //                                  }
    //                                  if(data['data'][x]['status'] === 'Completed') {
    //                                     col3 += gridView(data['data'][x]);
    //                                  }
    //                             } 
    //                         } else {
    //                             output = 'No data found';
    //                         }

    //                       }

    //                   }

    //                 } else {
    //                     output = '<tr><td colspan="6">No Data</td></tr>';
    //                 }

                      
                    
    //                 if(last_page > 1) {
                 
    //                     paginate += '<ul class="pagination pagination-ajax">';

    //                     for(var a = 1; a <= last_page; a++) {
                  

    //                           if( last_page > 1 && 1 == a  ) {
    //                             var is_disabled = ( current_page == 1 ) ? 'class="disabled"': '';
    //                             paginate += '<li><a href="javascript:void(0)" id="'+from+'" '+ is_disabled +'>«</a></li>';
    //                           }

    //                           if( current_page === a ) {

    //                             paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

    //                           } else {

    //                             paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

    //                           }

    //                           if( last_page > 1 && last_page == a ) {
    //                             var is_disabled = ( current_page == last_page ) ? 'class="disabled"': '';
    //                             paginate += '<li><a href="javascript:void(0)" id="'+last_page+'" '+ is_disabled +'>»</a></li>';
    //                           }
                            
    //                     }
    //                     paginate += '</div>';
    //                 }


    //                 if(  view == 'list' ) {
    //                     $('tbody').html(output);
    //                     $('.pagination-links').html(paginate);
    //                 } else {
    //                    $('#grid-wrapper').find('.col-todo .inner-list').html(col1);
    //                    $('#grid-wrapper').find('.col-progress .inner-list').html(col2);
    //                    $('#grid-wrapper').find('.col-completed .inner-list').html(col3);
    //                    $('.pagination-links').html(paginate);
    //                 }
    //              }
    //         });

    //   });

      /**
       * [description]
       * @param  {String} 
       * @return {[type]}      [description]
       */
      $('#clearBtn').on('click', function(e){
            return;
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
              var link = '{{ ( Auth::user()->isAdmin() === false ) ? URL::to('searchUserTask') : URL::to('searchTask') }}';
              
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
                                  output = '<tr><td colspan="8">No data found</td></tr>';
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
                        output = '<tr><td colspan="8">No data found</td></tr>';
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
             var creator_photo_link   = ticket['creator_photo_link'];
             var creator_name     = ticket['creator_name'];
             var total_tasks  = ticket['total_tasks'];
             var url          = ticket['link'];
             var product      = ticket['product'];

                output += '<tr class="single-task">';
                    output += '<td>';
                     output += '<strong>'+ id + '</strong>';
                    output += '</td>';
                    output += '<td>';
                        output += '<div class="task-owner">';
                            output += '<div class="img">';
                                output += '<div class="photo">';
                                 output += '<img src="'+ creator_photo_link +'" width="100%" />';
                                output += '</div>';
                            output += '</div>';
                            output += '<div class="info">';
                              output += '<strong>'+ creator_name +'</strong>';
                            output += '</div>';
                        output += '</div>';    
                    output += '</td>';
                   output += '<td>'; 
                        output += '<div class="task-info">';
                            output += '<p class="'+ type_color +'"><strong>'+ type +'</strong></p><span>'+ summary +'</span>';
                        output += '</div>';
                    output += '</td>';  
                    output += '<td>'; 
                        output += '<div class="task-product">';
                         output += '<span>'+ product +'</span>';
                        output += '</div>';
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
                        output += '<div class="task-status">';
                         output += '<span class="'+ status_color + ' box-bg">'+ status +'</span>';
                        output += '</div>';
                    output += '</td>'; 
                    output += '<td>'; 
                        output += '<div class="task-date">';
                         output += '<span>'+ created_at +'</span>';
                        output += '</div>';
                    output += '</td>'; 
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

                case 'Resolved':
                   color = 'bg-color-6';
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
            var url        = '{{ url('admin/softwares') }}';

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

