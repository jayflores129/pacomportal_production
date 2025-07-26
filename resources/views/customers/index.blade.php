@extends('layouts.app')

@section('content')
<section class="user-page">
    @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
      <div class="panel panel-top">
          <div class="row">
              <div class="col-sm-6">
                {!! Breadcrumbs::render('customers') !!}
              </div>
              <div class="col-sm-6 text-right">
            
              </div>
          </div>
      </div> 
    @endif 
   <div class="row">
      <div class="col-sm-12">
            <div class="panel panel-default panel-brand">
                <div class="panel-heading">
                  <h3>All Customers</h3>
                </div>
                <div class="panel-body">
                  <div class="table-responsive"> 
                    <table class="table table-striped table-default-brand">
                       <tr class="hidden-xs">
                           <th>Name</th>
                           <th>Country</th>
                           <th>Email</th>
                           <th>Company</th>
                           <th>Phone</th>
                           <th>Date Added</th>
                           <th width="100">Action</th>
                       </tr>

                       @foreach ( $users  as $user)       

                             <tr class="hidden-xs">
                                 <td>{{ $user->firstname .' '. $user->lastname }}</td>
                                 <td>{{ $user->country }}</td>
                                 <td>{{ $user->email }}</td>
                                 <td>{{ $user->company }}</td>
                                  <td>{{ $user->phone }}</td>
                                 <td>{{ date('F d, Y', strtotime($user->created_at)) }}</td>
                                 <td>
                                     <a href="{{ route('customers.show', $user->id)}}" class="btn-brand-icon btn-brand-primary"><i class="fa fa-eye btn-icon"></i><span>View</span></a>
                                 </td>
                             </tr>

                       @endforeach

                      
                    </table>
                  </div>

                 {{ $users->links() }}

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
</style>
@stop
