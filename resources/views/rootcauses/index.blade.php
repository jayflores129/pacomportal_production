@extends('layouts.app')

@section('content')

    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="grid justify-space-between">
          <div class="col">
            {!! Breadcrumbs::render('rootcause') !!} 
          </div>
          <div class="col text-right">
            <a href="{{ url('admin/rootcause/create') }}" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-check btn-pencil"></i><span>Create New Root Cause</span></a>
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
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-brand">
                <div class="panel-heading">
                  <h3>All Root Causes</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive"> 
                       <table class="table table-striped table-default-brand">
                         <tr>
                             <th width="200">Date Added</th>
                             <th>Name</th>
                             <th>Description</th>
                             <th width="250">Action</th>
                         </tr>
                        @foreach($rootcauses as $rootcause)
                         <tr>
                             <td>{{ $rootcause->created_at }}</td>
                             <td>{{ $rootcause->name }}</td>
                             <td>{{ $rootcause->description }}</td>
                             <td>
                              <ul class="list-inline">
                                <li><a href="{{ url('admin/rootcause') }}/{{ $rootcause->id }}/edit" class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-edit btn-icon"></i><span>Edit</span></a></li>
                                <li>
                                  {!! Form::open([
                                 'method' => 'delete',
                                 'route' => ['rootcause.destroy', $rootcause->id]
                                ]) !!}

                                 <button type="submit" class="btn-brand btn-brand-icon btn-brand-danger"><i class="fa fa-check btn-trash"></i><span>Delete</span></button>

                               {!! Form::close() !!}
                                </li>
                              </ul>
                              

                            </td>
                         </tr>
                         @endforeach
                     </table>
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
                </div>
            </div>
        </div>
    </div>

@endsection

