@extends('layouts.app')

@section('content')
   @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="grid justify-space-between">
          <div class="col">
            {!! Breadcrumbs::render('issues') !!} 
          </div>
          <div class="col text-right">
            <a href="{{ url('admin/issues/create') }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-pencil"></i><span>Create issue</span></a>
          </div>
        </div>
      </div> 
    @endif
    <div class="flash-message">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
          @if(Session::has('alert-' . $msg))

          <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
          @endif
        @endforeach
    </div> 
    <div class="panel panel-default panel-brand">
      <div class="panel-heading">
        <h3>All Issues</h3>
      </div>
      <div class="panel-body">
            <div class="table-responsive">         
             <table class="table table-striped table-issues">
               <tr>
                   <th >Name</th>
                   <th>Description</th>
                   <th>Date Added</th>
                   <th width="250">Action</th>
               </tr>
               @foreach($issues as $issue)
               <tr>
                   <td>{{ $issue->name }}</td>
                   <td>{{ $issue->description }}</td>
                   <td>{{ $issue->created_at }}</td>
                   <td>
                    <ul class="list-inline">
                      <li>
                        <a href="{{ url('/admin/issues/') }}/{{$issue->id}}/edit" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-pencil"></i><span>Edit</span></a>
                     </li>
                      <li>
                        {!! Form::open([
                         'method' => 'delete',
                         'route' => ['issues.destroy', $issue->id]
                        ]) !!}

                       <button type="submit" class="btn-brand btn-brand-icon btn-brand-danger"><i class="fa fa-check btn-trash"></i><span>Delete</span></button>
                       {!! Form::close() !!}
                      </li>
                    </ul>
                    
                
                  </td>
               </tr>
               @endforeach
            </table>

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
        </div>
    </div>
@endsection
