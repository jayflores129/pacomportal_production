@extends('layouts.app')

@section('content')

  @if(Auth::user()->isAdmin())

    <div class="panel panel-top">

      <div class="grid justify-space-between">

        <div class="col">

             {!! Breadcrumbs::render('user') !!}

        </div>

        <div class="col" style="display:flex;align-self: center;">

           @if(Auth::user()->id === $user->id || Auth::user()->isAdmin())

              <ul class="list-inline">

                @if ($user->id !=  Auth::user()->id && !$user->blocked && !$user->isAdmin() )

                  <li>        

                      {!! Form::open([
                           'method' => 'patch',
                           'route' => ['users.block_user', $user->id]
                          ]) !!}

                          <button type="submit" class="btn-brand btn-brand-icon btn-brand-success delete-user"><i class="fa fa-close btn-trash"></i><span style="color: #222;">Block</span></button>
                    {!! Form::close() !!}

                  </li>

                @elseif ($user->id !=  Auth::user()->id && $user->blocked && !$user->isAdmin() )
                
                  <li>        

                      {!! Form::open([
                           'method' => 'patch',
                           'route' => ['users.unblock_user', $user->id]
                          ]) !!}

                          <button type="submit" class="btn-brand btn-brand-icon btn-brand-success delete-user"><i class="fa fa-close btn-trash"></i><span style="color: #222;">Unblock</span></button>
                    {!! Form::close() !!}

                  </li>

                @endif 

                @if ($user->id !=  Auth::user()->id)

                  <li>  

                      {!! Form::open([
                           'method' => 'delete',
                           'route' => ['users.destroy', $user->id]
                          ]) !!}

                          <button type="submit" class="btn-brand btn-brand-icon btn-brand-danger delete-user"><i class="fa fa-trash btn-trash"></i><span style="color: #222;">Delete</span></button>
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

  <div class="panel panel-default panel-brand">

    <div class="panel-heading">

        <h3>User Information - USER ID #{{ $user->id }}</h3>

    </div>

        @if(!empty($usermeta) )

          @foreach($usermeta as $meta)

             @php
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
             @endphp

          @endforeach

       @else

            @php
              $address      = '';
              $address2     = '';
              $city         = '';
              $state        = '';
              $zipcode      = '';
              $fax          = '';
              $sms_number   = '';
              $office_phone = '';
              $website        = '';
            @endphp

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

                        <p>{{ $user->company }}</p>

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
  
@endsection
