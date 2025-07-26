@extends('layouts.app')

@section('content')
<section class="user-page">
    @if(Auth::user()->isAdmin())
      <div class="panel panel-top">
        <div class="row">
          <div class="col-sm-6">
            {!! Breadcrumbs::render('pendingUser') !!} 
          </div>
          <div class="col-sm-6 text-right">
          </div>
        </div>
      </div> 
    @endif
    @include('components/flash')
    <div class="list-to-approve">
      <div class="panel panel-default panel-brand">
          <div class="panel-heading">
            <h3>All Approval</h3>
          </div>
          <div class="panel-body">
              <div class="table-responsive"> 
                 <table class="table table-default-brand table-striped">
                     <tr class="table-heading-primary">
                         <th>Name</th>
                         <th>Email</th>
                         <th>Company</th>
                         <th>Date Added</th>
                         <th width="200">Action</th>
                     </tr>
                     @if( !empty($users) )
                       @foreach ( $users as $user)
                          @if ($user->id !==  $userID)
                               <tr>
                                   <td><a href="{{ url('profile/') }}/{{$user->id}}">{{ $user->firstname }} {{ $user->lastname }}</a></td>
                                   <td>{{ $user->email }}</td>
                                   <td>{{ $user->company }}</td>
                                   <td>{{ date('F d, Y', strtotime($user->created_at)) }}</td>
                                   <td>
                                    <ul class="list-inline">
                                      <li>
                                        <a href="{{ url('/admin/users/') }}/{{ $user->id }}">View details</a>
                                      </li>
                                    </ul>     
                                       
                                   </td>
                               </tr>
                          @endif
                       @endforeach
                     @endif   
                     @if( empty($users) )
                        <tr><td>No users to approve or disapprove</td></tr>
                     @endif  
                    
                 </table>
              </div>
                 {{ $users->links() }}
                </div>
            </div>
        </div>

</section>
@endsection

@section('css')
<style>
    .table a {
      font-weight: 600;
    }
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
</style>
@stop
