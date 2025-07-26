@extends('layouts.app')

@section('content')
    @if( Auth::user()->isAdmin() )
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
          Click the button to subscribe a user
       @endslot
       @if( $subscribers )
         <div class="contact-list">
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
                     <td>
                        {!! Form::open([
                             'method' => 'patch',
                             'route' => ['subscribeUser', $subscriber->id]
                            ]) !!}
                            <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-check"></i> <span>subscribe</span></button>
                        {!! Form::close() !!}
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
