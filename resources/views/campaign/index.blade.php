@extends('layouts.app')

@section('content')

    @if( Auth::user()->isAdmin())

      <div class="panel panel-top">

          <div class="row">

              <div class="col-sm-6">

                {!! Breadcrumbs::render('settings') !!}

              </div>

              <div class="col-sm-6 text-right">
              
              </div>

          </div>

      </div> 

    @endif

    @include('components/flash')

    @component('components/panel')

       @slot('title')

          Subscribers List

       @endslot

       @if( $subscribers )

         <div class="contact-list">

            <div class="search-section" style="margin-bottom: 20px;max-width: 400px;">

               <p>Quick Search </p>

               <form style="display: flex;justify-content: space-between;">

                 <div style="width: calc(100% - 140px);margin: 0;">

                    <input type="text" id="search" name="search" value="{{request('search')}}" class="placeholder" style="margin: 0;height: 35px;" />

                 </div>

                 <button type="submit" class="btn-brand btn-brand-info btn-brand-icon" style="width: 135px;"><i class="btn-icon fa fa-refresh"></i> <span>Search</span></button>

               </form>

            </div>

            @component('components/table')

               @slot('heading')

                 <tr>

                   <td><strong>Index</strong></td>

                   <td><strong>Company</strong></td>

                   <td><strong>Full Name</strong></td>

                   <td><strong>Email</strong></td>

                   <td width="200"><strong>Action</strong></td>

                 </tr>

               @endslot

               @php $count = 0  @endphp

               @foreach($subscribers as $subscriber)

                  @php $count++ @endphp

                   <tr>

                     <td>{{ $count }}</td>

                     <td>{{ $subscriber->company }}</td>

                     <td>{{ $subscriber->firstname . ' ' . $subscriber->lastname }}</td>

                     <td>{{ $subscriber->email }}</td>

                     <td><a href="{{ url('admin/subscriber/') }}/{{$subscriber->id}}"  class="btn-brand btn-brand-danger btn-brand-icon"><i class="btn-icon fa fa-close"></i> <span>unsubscribe</span></a>

                      </td>

                   </tr>

               @endforeach

            @endcomponent

         </div>  

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

       @endif  
       
    @endcomponent

@endsection


@section('css')
<style>
  .contact-list {
    max-width: 100%;
  }
  .contact-list .grid > div {
    padding: 10px 5px;
  }
</style>
    
@stop


@section('js')

    <script>

       $('#search').on('keyup', function(){
            return
            var value     = $(this).val();
            var link      = '{{ URL::to('admin/search-subscriber') }}';


            $.ajax({
                 type : 'get',
                 url  : link,
                 data: {
                  'search' : value
                 },
                 success: function(data) {
            
                    var contacts         = data['contacts'];
                    var total_items  = data['contacts'].length;
                    var output       = '';


                    if( total_items > 0 ) {

                        for(var x = 0; x < total_items; x++ ) {
                       
                           output += listView(data['contacts'][x], x);
                        } 

                    } else {

                        output = '<tr><td colspan="5">No data found</td></tr>';

                    }
                  
                    $('tbody').html(output);
                    $('.pagination-links').html('');
            
                 }
            });

       });
      
      function listView( contact = [], count ) {

             var output           = '';
             var fullname         = contact['firstname'] + ' ' + contact['lastname'];
             var email            = contact['email'];
             var company          = contact['company'];
             var link             = '{{ url('admin/subscriber/') }}/' + contact['id'];



                output += '<tr>';
                     output += '<td>' + ( count + 1) + '</td>';
                     output += '<td>' + company + '</td>';
                     output += '<td>' +  fullname + '</td>';
                     output += '<td>' +   email + '</td>';
                     output += '<td>';
                     output += '<a href="' + link  +'"  class="btn-brand btn-brand-danger btn-brand-icon"><i class="btn-icon fa fa-close"></i> <span>unsubscribe</span></a>';
                      output += '</td>';
                   output += '</tr>';


            return output;
       }
  </script>
@stop