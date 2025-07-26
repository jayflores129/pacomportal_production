@extends('layouts.app')

@section('content')
<section class="user-page">
    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('userPermission') !!} 
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
    @endif
   
   <div class="row change-permission">
      <div class="col-sm-12">

            <div class="panel panel-default panel-brand">
                <div class="panel-heading">
                  <h3>Manage Permission</h3>
                </div>
                <div class="panel-body">
                      <div class="table-responsive"> 
                       <table class="table table-default-brand table-striped">
                        <thead>
                            <tr>
                               <th>Name</th>
                               <th>Company</th>
                               <th>Date Added</th>
                               <th width="500">Action</th>
                           </tr>
                        </thead>
                           
                           @foreach ( $users as $user)

                                   <?php  $role_id = DB::table('model_has_roles')->where('model_id', $user->id)->value('role_id');

                                   ?>
                                   <tr>
                                       <td>{{ $user->firstname .' '. $user->lastname }}</td>
            
                                       <td>{{ $user->company }}</td>
                                       <td>{{ date('F d, Y', strtotime($user->created_at)) }}</td>
                                       <td>
                                        @if(Auth::user()->id != $user->id )
                                        {{ Form::open(array('url' => 'admin/process-permission/' . $user->id )) }}
                                          <div class="row">
                                              <div class="col-sm-8">
                                            <select name="permission" class="form-control" id="permission">
                                                @foreach($roles as $role)
                                                    @if($role_id == $role->id)
                                                      <option value="{{ $role->id }}" selected>{{ $role->text }}</option> 
                                                    @else
                                                      <option value="{{ $role->id }}">{{ $role->text }}</option> 
                                                    @endif

                                                @endforeach
                                            </select>
                                              </div>
                                              <div class="col-sm-4">

                                                <button type="submit" class="btn-brand btn-brand-icon btn-brand-success"><i class="fa fa-check btn-check"></i><span>Save Changes</span></button>
                                              </div>
                                         </div>
                                        {!! Form::close() !!}  
                                        @endif              
                                  
                                       </td>
                                   </tr>
         
                           @endforeach

                       </table>
                      </div>

                      @if (isset($pagination))
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
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
      <div class="col-sm-12">
        
        
            <div class="panel panel-default">

                <div class="panel-header">
                   <h3 class="heading">Legend</h3>
                </div>
                <div class="panel-body">

                  Roles and Permissions will be updated soon
                </div>
            </div>     
            

      </div>
   </div>




</section>
@endsection

@section('css')
<style>
    .option-nav ul {
        padding: 0;
    }
    .panel .panel-header {
        padding: 10px;
        background: #2d2d2d;
    }
    .panel .panel-header .heading {
          margin: 0;
          font-size: 1.2em;
          color: #fff;
    }
    .legend-list {
      padding: 0 0 0 10px;

    }
    .legend-list li {
      margin-bottom: 10px;
    }
    .form .radio {
      display: inline-block;
      margin: 10px 20px 10px 0;
    }
    .input-warranty input {
      margin-right: 10px;
    } 
    #permission {
      text-transform: capitalize;
    }
</style>
@stop
