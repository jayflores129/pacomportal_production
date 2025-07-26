<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Pacom') }}</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('public/images/favicon.png') }}" />

    <title>{{ config('app.name', 'Pacom') }}</title>

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Latest compiled and minified CSS -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600|Roboto:300,400,600,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" media="print" type="text/css">
    
    @yield('css')

    <style>
      #google_translate_element > div > div {
        margin-right: 10px;
      }
      #google_translate_element > div select {
         height: 23px;
      }
      div#google_translate_element > div {
          display: flex;
          justify-content: center;
          align-items: center;
      }
      ul.vertical-menu > li.active > a {
          pointer-events: none;
      }
      li.menu-item.parent-item.active.show-submenu ul {
          display: block !important;
      }
     .form-control, input[type=email], input[type=password], input[type=text], select, select[type=text],select[type='date'], textarea {
          width: 100%;
          height: 38px;
          padding: 0 10px;
          margin-bottom: 10px;
          border: 1px solid #e7e7e7;
          background: #fbfbfb;
          box-shadow: none;
      }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.1/TweenMax.min.js"></script> --}}
  

<script type="text/javascript" 
src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<!-- ./app -->
 <div id="app">
    <header class="site-header">
        <div class="container-fluid">
            <div class="inner-header">
              <!-- Header Left -->
              <div class="header-left">
                  <div class="top-button-menu">
                    <button id="sideMenu">
                      <span class="fa fa-bars"></span>
                    </button>
                  </div>
                  <!-- Branding Image -->
                  <a class="site-logo" href="{{ url('/home') }}">
                      <img src="{{ asset('public/images/pacom_logo.jpg') }}" width="100" />
                  </a>
                  <ul class="top-menu">
                     <li><a href="https://pacom.com/home"><i class="fa fa-home"></i> <span>Homepage</span></a></li>
                     <li><a href="https://pacom.com/en/contact-us-online"><i class="fa fa-phone"></i> <span>Contact Us</span></a></li>
                     <li><div id="google_translate_element"></div></li>
                  </ul>
              </div>
              <!-- // Header Left -->
              <!-- Header Right -->
              <div class="header-right">
                   <!-- Right Side Of Navbar -->
                  <ul class="site-navigation">
                      <!-- Authentication Links -->
                      @if (Auth::guest())
                          <li><a href="{{ route('login') }}">Login</a></li>
                          <li><a href="{{ url('registration') }}">Register</a></li> 
                      @else
                         
                          <li>
                              <a href="#" class="notification-group-icon" id="menuSearch" data-toggle="dropdown" role="button" aria-expanded="false">
                                 <span class="fa fa-search"></span>
                              </a>
                          </li>
                         

                          <li class="hidden-xs">
                              <a href="{{ url('admin/softwares') }}"  class="notification-group-icon"  data-toggle="tooltip" data-placement="bottom" title="Tasks">
                                 <span class="fa fa-tasks"></span>
                              </a>
                          </li>
                          <li class="hidden-xs">
                              <a href="{{ url('/repairs') }}"  class="notification-group-icon"  data-toggle="tooltip" data-placement="bottom" title="Repairs">
                                 <span class="fa fa-wrench"></span>
                              </a>
                          </li>
                          <li class="dropdown">
                              <a href="#" class="dropdown-toggle notification-group-icon" data-toggle="dropdown" role="button" aria-expanded="false"  data-toggle="tooltip" data-placement="bottom" title="Notifications">
                                 <span class="fa fa-bell"></span>
                                 @if( auth()->user()->unreadnotifications->count() )
                                  <span class="badge badge-light">{{ auth()->user()->unreadnotifications->count() }}</span>
                                 @endif 
                              </a>
                              @if( auth()->user()->unreadnotifications->count() > 0 ) 

                              <ul class="dropdown-menu" role="menu">
                                  @foreach( auth()->user()->unreadnotifications as $notification)

                                    <li><a href="{{ $notification->data['link'] }}">{{ $notification->data['data'] }}</a></li>

                                  @endforeach
                                  <li><a href="{{ route('markRead') }}" class="marker">Mark all as read</a></li>
                              </ul>
                              @endif
                          </li>
                          <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                             
                                <?php $photo = DB::table('user_details')->where('user_id', Auth::user()->id )->value('photo'); ?>
                                    @if( $photo )
                                      
                                        <img src="{{ asset('public/images/uploads/' . $photo ) }}"  width="24" />
                                    
                                    @else
                                     
                                        <img src="{{ asset('public/images//user-placeholder.png') }}" width="24" />
                                   
                                    @endif
                                    <span class="caret"></span>
                              </a>

                              <ul class="dropdown-menu" role="menu">
                                  <li>
                                      <a href="{{ url('profile') }}">
                                          Your Profile
                                      </a>
                                  </li>
                                  <li>
                                      <a href="{{ route('logout') }}"
                                          onclick="event.preventDefault();
                                                   document.getElementById('logout-form').submit();">
                                          Logout
                                      </a>

                                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                          {{ csrf_field() }}
                                      </form>
                                  </li>
                              </ul>
                          </li>
                      @endif
                  </ul>
              </div>
              <!-- // Header Right -->
            </div> 
      </header>
      <div class="top-search-box">
        <div class="container-fluid">
            <input type="text" name="search" id="inputTopSearch" placeholder="Search ..." />

            <div id="topSearchResults"></div>
            <div class="top-pagination-links"></div>
        </div>
      </div>

        <!-- ./main-content -->
        <main id="content" class="main-content">

                  <!-- ./sidebar -->
                  <div class="sidebar">

                    @include('components/navigation')

                  </div>
                   <!-- ./sidebar -->

                  <!-- ./inner-content -->
                  <div class="inner-content">    

                       @yield('content')

                  </div><!-- ./main-content -->

  
         </main>
         <!-- ./sidebar -->

    </div>
    <!-- ./app -->

    <!-- Scripts -->
    <!-- Latest compiled and minified JavaScript -->
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="{{ asset('public/js/app.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
    <script>
        function searchTables( value, items, page_id )
        {


                $.ajax({
                   type : 'get',
                   url  : '{{ url('/searchAnything') }}', 
                   data: {
                    'search' : value
                  },
                   success: function(data) {
                    var data       = data;
                    var total      = data.length;
                    var output     = '';
                    var start_page = parseInt(items);  
                    var end_page   = items * page_id;

                     //console.log(data);
                     if( total < end_page ) {
                        end_page = total;
                     }


                     if(page_id == 1  || page_id == '' )
                     {
                        start_page = 0;
                        end_page = items;

                     } else {

                        start_page = (items * page_id) - items;

                     }

                     //console.log('l page :'+ total);

                    for(var x = 0; x < total; x++) {
                      //console.log(data[x]);
                      // display title, description, category, link
                      output +=  '<div class="item">';
                        output +=  '<div class="row">';
                           output +=  '<div class="col-sm-12">';
                            output +=  '<h3>'+ data[x]['title'] +'</h3>';
                            output +=  '<p>'+ data[x]['description'] +'</p>';
                           output +=  '</div>';
                        output +=  '</div>';
                        output +=  '<div class="row">';
                           output +=  '<div class="col-sm-6">';
                              output +=  '<div class="category">';
                                output +=  data[x]['category'];
                              output +=  '</div>';
                           output +=  '</div>';
                           output +=  '<div class="col-sm-6">';
                            output +=  '<a href="'+ data[x]['link'] +'">View More</a>';
                           output +=  '</div>';
                        output +=  '</div>';
                      output +=  '</div>';

                    }

                      var itemsLeft = '';

                      if( total % items != 0 ) {
                        //console.log('test total');
                        itemsLeft = Math.ceil( ( total / items) );
                      } else {
                        itemsLeft = Math.ceil( total / items );
                      }

                      //console.log("pages : " + itemsLeft);
                      //console.log('items per page :' + items);

                      var link = '<a href="#" class="btn-brand btn-next" id="'+ itemsLeft  +'">Next Page</a>';

                      var paginate = '';

                      if(itemsLeft > 0) {

                          paginate += '<ul class="pagination pagination-ajax">';

                          for(var a = 1; a <= itemsLeft; a++) {

                              if( page_id == a || page_id == '' && a === 1) {

                                paginate += '<li class="active"><span id="'+ a +'" class="page-link">'+ a +'</span></li>';

                              } else {

                                paginate += '<li><a href="javascript:void(0)" id="'+ a +'" class="page-link">'+ a +'</a></li>';

                              }

                          }
                          paginate += '</div>';
                      }

                      $('#topSearchResults').html(output);
                      $('.top-pagination-links').html(paginate);
                   }
              });
            }    


                /**
               * [description]
               * @param  {[type]} )   value [description]
               * @return {[type]}     [description]
               */
              $('#inputTopSearch').on('keyup', function(){

                        var value = $(this).val();
                        var items = 5;

                        searchTables(value, items);


                        if(value === '')
                        {
                          $('#topSearchResults').html('');
                          $('.top-pagination-links').html('');
                        }

                   });

            $('.top-pagination-links').on('click', '.page-link', function(e){

                      e.preventDefault();

                      var page_id  = $(this).attr('id');
                      var value = $('#inputTopSearch').val();
                      var items = 5;
                      var output2 = '';


                      searchTables(value, items, page_id);

                      //$('#topSearchResults').html(output);
                      //$('.top-pagination-links').html(paginate);
            });

            // $('.vertical-menu li.active').on('click', function() {
            //     return;
            // })

    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#consignment_note'))
            .catch(error => {
                console.error(error);
            });
            ClassicEditor
            .create(document.querySelector('#RmaComment'))
            .catch(error => {
                console.error(error);
            });
             
              if (location.pathname != '/repairs') {
                if (!location.pathname.includes('repairs'))
                  localStorage.removeItem('RmaIDs');
              }
              
    </script>
    </script>
    <script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
      }
      googleTranslateElementInit();
     </script>
    
    @yield('js')

</body>
</html>
