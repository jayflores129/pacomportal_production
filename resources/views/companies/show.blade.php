@extends('layouts.app')

@section('content')
  @if(Auth::user()->isAdmin())
    <div class="panel panel-top">
      <div class="grid justify-space-between">
        <div class="col">
             {!! Breadcrumbs::render('viewCompany') !!}
        </div>
        <div class="col" style="display:flex;align-self: center;">
           @if( Auth::user()->isAdmin())
              <ul class="list-inline">
                <li>
                 <a href="{{ url('admin/companies/') }}/{{ $company->id }}/edit" class="btn-brand btn-brand-primary btn-brand-icon"><i class="btn-icon fa fa-pencil"></i><span>Edit</span></a>
                </li>
              </ul> 
            @endif  
        </div>
      </div>
    </div> 
  @endif
  @include('components/flash')
<div class="panel panel-default panel-brand">
    <div class="panel-heading">
        <h3>Company information - COMPANY ID #{{ $company->id }}</h3>
    </div> 
    <div class="panel-body">
        <div class="profile-section">
            <div class="profile-info">

                <div class="row">
                    <div class="col-sm-6">
                      <div class="field field-group clearfix">
                        <label for="input_cn">Name</label>
                        <p>{{ $company->name }}</p>
                      </div>
                      <div class="field field-group clearfix">
                        <label for="input_cn">Description</label>
                        <p>{{ $company->description }}</p>
                      </div>
                      <div class="field field-group clearfix">
                        <label for="input_cn">Email</label>
                        <p>{{ $company->email }}</p>
                      </div>
                     <div class="field field-group clearfix">
                        <label for="input_sn">Telephone Number</label>
                        <p>{{ $company->telephone_no }}</p>
                      </div>
                    <div class="field field-group clearfix">
                      <label for="input_sn">Fax</label>
                      <p>{{ $company->fax }}</p>
                    </div>
                    <div class="field field-group clearfix">
                      <label for="input_sn">Contact Person</label>
                      <p>{{ $company->contact_person }}</p>
                    </div>
                  </div>

                    <div class="col-sm-6">
                      <div class="field field-group clearfix">
                        <label for="input_cn">Address</label>
                        <p>{{ $company->address }}</p>
                      </div>

                      <div class="field field-group clearfix">
                        <label for="input_i">Country</label>
                        <p>{{ $company->country }}</p>
                      </div>
                      <div class="field field-group clearfix">
                        <label for="input_i">Currency</label>
                        <p>{{ $company->currency }}</p>
                      </div>
                    </div>

                </div>
            </div>
          </div>    

    </div>
</div>
@component('components/panel')
  @slot('title')
    All Users
  @endslot
  @component('components/table')
     @slot('heading')
      <tr>
        <td><strong>ID</strong></td>
        <td><strong>Name</strong></td>
        <td><strong>Country</strong></td>
        <td><strong>Email</strong></td>
        <td><strong>Phone</strong></td>
        <td><strong>Action</strong></td>
      </tr>
     @endslot

    
       @if( $users )
         @foreach($users as $user)
          <tr>
             <td>{{ $user->id }}</td> 
             <td>{{ $user->firstname . ' ' . $user->lastname  }}</td> 
             <td>{{ $user->country }}</td>
             <td>{{ $user->email }}</td>
             <td>{{ $user->phone }}</td>
             <td>{!! link_to_route('users.show', 'View Info', ['user' => $user->id ]) !!}</td>
          </tr> 
         @endforeach
       @endif
  @endcomponent  
@endcomponent



@endsection
