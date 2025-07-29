@extends('layouts.app')

@section('content')

  @if(Auth::user() && Auth::user()->isAdmin() )

    <div class="panel panel-top">

      <div class="grid justify-space-between">

        <div class="col">

             {!! Breadcrumbs::render('user') !!}

        </div>

        <div class="col" style="display:flex;align-self: center;">

           @if(Auth::user()->id === $user->id || Auth::user()->isAdmin())

              <ul class="list-inline">

                <li>

                   <a href="{{ url('admin/users/') }}/{{ $user->id }}/edit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-pencil"></i><span>Edit</span></a>

                </li>

                @if(empty( $user->status) )

                  <li>

                     {{ Form::open(array('url' => 'admin/approving-user/' . $user->id )) }}

                      <input type="hidden" name="status" value="1" />

                      <button type="submit" class="btn-brand btn-brand-success btn-brand-icon" {{ ( $user->company_id == '') ? 'disabled="disabled"': '' }}><i class="btn-icon fa fa-check"></i> <span>Approve</span></button>

                     {!! Form::close() !!}  

                  </li>

                  <li>

                    {{ Form::open(array('url' => 'admin/disapproving-user/' . $user->id )) }}

                     <button type="submit" class="btn-brand btn-brand-danger btn-brand-icon" {{ ( $user->company_id == '') ? 'disabled="disabled"': '' }}><i class="btn-icon fa fa-close"></i> <span>Disapprove</span></button>

                     {!! Form::close() !!} 

                  </li>

                  @endif

              </ul> 

            @endif  

        </div>

      </div>

    </div> 

  @endif

  @include('components/flash')


  @if( $user->company_id == '')

    <div class="flash-message"><p class="alert alert-info">Note: Connect this account to the company from the dropdown below. If the company is not in the system, please do add it first. The "approve" and "disapprove" buttons are disabled until this user is connected to the company.</p></div>
  
  @endif  
      <div class="panel panel-default" style="min-height: auto">

        <div class="panel-body form-company">
 
            @if($companies->count() > 0 && $hasCompany)

                <h4>Please choose the company of the user.</h4>

                {!! Form::open(['route' => array('users.updateCompany', $user->id),'method' => 'patch' ]) !!}

                <div class="grid" style="justify-content: flex-end;align-items:center;">

                  <div class="col" style="width: 100%;max-width:300px; margin-right: 5px;">

                      <select name="company" style="height: 33px;">

                        @foreach( $companies as $row )

                            <option value="{{ $row->id }}">{{ $row->name }}</option>

                        @endforeach

                      </select>

                  </div>
 
                  <div class="col">
                       <input type="hidden" name="user_id" value="{{  $user->id }}" />
                        <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-check"></i> <span>Update Company</span></button>

                  </div>

                </div>

                {!! Form::close() !!}

            @else

              {!! Form::open(['route' => array('users.addCompany', $user->id),'method' => 'patch' ]) !!}
              <div class="grid" style="justify-content: flex-end;">

                <div class="col" style="width: 100%;max-width: 300px; margin-right: 5px;">

                      <select name="company" style="height: 33px;">

                        @foreach( $allCompanies as $row )

                            <option value="{{ $row->id }}">{{ $row->name }}</option>

                        @endforeach

                      </select>

                  </div>

                  <div class="col">
                     <input type="hidden" name="user_id" value="{{  $user->id }}" />

                        <button type="submit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-check"></i> <span>Add New Company</span></button>

                  </div>

                </div>

              {!! Form::close() !!}

            @endif

        </div>

      </div>
  


  <div class="panel panel-default panel-brand">

    <div class="panel-heading">

        <h3>User Information - USER ID #{{ $user->id }}</h3>

    </div>

        @if(!empty($usermeta) )

          @foreach($usermeta as $meta)
             <?php 
              $address      = $meta->address;
              $address2     = $meta->address2;
              $city         = $meta->city;
              $state        = $meta->state;
              $zipcode      = $meta->zipcode;
              $fax          = $meta->fax;
              $sms_number   = $meta->sms_number;
              $office_phone = $meta->office_phone;
              $website      = $meta->website;
              $photo        = $meta->photo; 
             ?>
          @endforeach

       @else

       <?php 

              $address      = '';
              $address2     = '';
              $city         = '';
              $state        = '';
              $zipcode      = '';
              $fax          = '';
              $sms_number   = '';
              $office_phone = '';
              $website        = '';


             ?>
       @endif  
    <div class="panel-body">

        <div class="profile-section">

            <div class="profile-photo">

                  @if( !empty($photo) )

                    <div class="photo">

                      <img src="{{ asset('images/uploads/' . $photo ) }}"  width="100%" />

                    </div>

                  @else

                    <div class="photo">

                      <img src="{{ asset('images/user-placeholder.png') }}" width="100%" />

                    </div>

                  @endif

            </div>

            <!-- ./left-section --> 
            <div class="profile-info">

                <div class="row">

                    <div class="col-sm-4">

                      <div class="field field-group clearfix">

                        <label for="input_cn">First Name</label>

                        <p>{{ $user->firstname }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">Last Name</label>

                        <p>{{ $user->lastname }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">Email</label>

                        <p>{{ $user->email }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_sn">Phone Number</label>

                        <p>{{ $user->phone }}</p>

                      </div>

                    </div>

                    <div class="col-sm-4">

                      <div class="field field-group clearfix">

                        <label for="input_cn">Address</label>

                        <p><?php echo $address ?></p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">Address 2</label>

                        <p>{{ $address2 }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">City</label>

                        <p>{{ $city }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">State</label>

                        <p>{{ $state }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">Zip Code</label>

                        <p>{{ $zipcode }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_i">Country</label>

                        <p>{{ $user->country }}</p>

                      </div>

                    </div>

                    <div class="col-sm-4">

                      <div class="field field-group clearfix">

                        <label for="input_cn">Company</label>
                        
                           @if($userCompanies->count() > 0)
                              <select>
                                  @foreach($userCompanies as $item)
                                    <option>{{ optional($item->company)->name }}</option>
                                  @endforeach
                              </select> 
                            @endif
                        
                      </div>
                      <div class="field field-group clearfix">

                        <label for="input_cn">Fax</label>

                        <p>{{ $fax }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">SMS Number</label>

                        <p>{{ $sms_number }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_cn">Office Phone</label>

                        <p>{{ $office_phone }}</p>

                      </div>

                     <div class="field field-group clearfix">

                        <label for="input_cn">Website</label>

                        <p>{{ $website }}</p>

                      </div>

                      <div class="field field-group clearfix">

                        <label for="input_i">Date Created</label>

                        <p>{{ $user->created_at }}</p>

                      </div>

                    </div> 

                </div>

            </div>

          </div>    

    </div>

</div>

<div class="single-task-tab">

    <ul class="nav nav-tabs">

      <li class="active"><a data-toggle="tab" href="#allCompanies">All Companies</a></li>

      <li><a data-toggle="tab" href="#openticket">Open Tickets</a></li>

      <li><a data-toggle="tab" href="#allTickets">All Tickets</a></li>

      <li><a data-toggle="tab" href="#allTasks">All Created Tasks</a></li>

      <li><a data-toggle="tab" href="#allResolvedTasks">All Resolved Tasks</a></li>

      <li><a data-toggle="tab" href="#ActivityLog">Activity Log</a></li>

    </ul>

</div>
<div class="single-task-content tab-content" style="background: #fff;padding: 20px;margin-bottom: 20px;">

  <div id="allCompanies" class="tab-pane fade in active">

    @include('users/show/companies')

  </div>

  <div id="openticket" class="tab-pane fade in">

     @include('users/show/open-tickets')

  </div>

  <div id="allTickets" class="tab-pane fade in">

    @include('users/show/tickets')

  </div>

  <div id="allTasks" class="tab-pane fade in">

    @include('users/show/tasks')

  </div>

  <div id="allResolvedTasks" class="tab-pane fade in">

    @include('users/show/resolved-tasks')

  </div>

  <div id="ActivityLog" class="tab-pane fade in"> 

     @include('users/show/activity-log')

  </div>

</div> 


@endsection
@section('css')
<style>
  .form-company form {
       display: flex;
      align-items: center;
      justify-content: flex-end;
  }
</style>
@endsection