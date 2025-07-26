@extends('layouts.app')

@section('content')
<section class="customer-section">
    @if(Auth::user()->hasRole(['admin', 'super admin', 'SPG Internal User']))
      <div class="panel panel-top">
          <div class="row">
              <div class="col-sm-6">
                User Profile
              </div>
              <div class="col-sm-6 text-right">
            
              </div>
          </div>
      </div> 
    @endif    
    @include('components/flash')
    <div class="panel panel-default panel-brand">
        <div class="panel-heading">
            <div class="row">
              <div class="col-sm-6">
                <h3 class="heading">Customer Information</h3>
              </div>
              <div class="col-sm-6 text-right">
                @if(Auth::user()->id === $user->id)
                  <a href="{{ url('profile/') }}/{{ $user->id }}/edit">Edit</a>
                @endif
              </div>
            </div>
        </div>
        <div class="panel-body">
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
                  $website      = '';


                 ?>
           @endif
          <div class="profile-section">
                <div class="profile-photo">
                      
                     @if( !empty($photo) )
                        <div class="photo">
                          <img src="{{ url('images/' . $photo ) }}"  width="100%" />
                        </div>
                      @else
                        <div class="photo">
                          <img src="{{ asset('/public/images/user-placeholder.png') }}" width="100%" />
                        </div>
                      @endif
                </div>
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
                          <div class="field field-group clearfix">
                            <label for="input_sn">Company Name</label>
                            <p>{{ $user->company }}</p>
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

       </div><!-- ./panel body -->  
     </div><!-- ./panel -->  

    @component('components/panel')
        @slot('title')
           Contact Log
        @endslot
        <div class="panel-fix-height"> 
        <div class="table-responsive"> 
          <table class="table table-striped table-default-brand">
            <thead>
              <tr>
                <th width="300">Time</th>
                <th width="200">Type</th>
                <th>Descrption</th>
              </tr>
            </thead>
            <tbody>
               @if($logs)
                  @foreach($logs as $log)
                    <tr>
                      <td>{{ date(' d-m-Y H:i:s', strtotime($log->created_at)) }}</td>
                      <td>{!! $log->type !!}</td>
                      <td>{!! $log->description !!}</td>
                    </tr>
                  @endforeach
               @else
                  No logs
               @endif
            </tbody>
          </table>
         </div>  
        </div>   
    @endcomponent

    <div class="panel panel-default panel-brand">
        <div class="panel-heading">
            <h3 class="heading">All Repairs</h3>
        </div>
        <div class="panel-body repair-details">
               @if($repairs) 
                  <div class="repair-log">

                      <div class="table-responsive"> 
                       <table class="table table-striped table-default-brand">
                          <thead>
                           <tr >
                             <th>Ticket Number</th>
                             <th>Company</th>
                             <th>Product</th>
                             <th>Issue</th>
                             <th>Status</th>
                             <th>Under Warranty</th>
                             <th>Date Added</th>
                             <th>Action</th>
                         </tr>  
                        </thead>
                        <tbody class="repair-log">
        
                          <?php $count = 0; ?>
                          @foreach ($repairs as $repair )


                                <?php 
                                  $count++;
                                   if($repair->under_warranty) {
                                       $is_w = 'Yes' ;
                                   }
                                   else {
                                       $is_w = '';
                                   }
                                   $class = '';

                                   switch ($repair->status) {
                                     case 'open':
                                       $class = 'btn-default btn-open';
                                       break;

                                    case 'Partially Shipped':
                                       $class = 'btn-default btn-ps';
                                       break;
                                       
                                    case 'Completely Shipped':
                                       $class = 'btn-default btn-cs';
                                       break; 

                                      case 'received':
                                       $class = 'btn-default btn-r';
                                       break;
                                       
                                      case 'repaired':
                                       $class = 'btn-default btn-rp';
                                       break;  

                                      case 'returned':
                                       $class = 'btn-default btn-rt';
                                       break;  
                                     
                                     default:
                                       $class = '';
                                       break;
                                   }
                               ?> 
                                  <tr>
                                     <td>{{ $repair->id }}</td>
                                     <td>{{ $repair->company }}</td>
                                     <td>{{ $repair->product  }}</td>

                                     <td>{{ $repair->issue }}</td>
                                     <td><span class="{{ $class }}">{{ $repair->status }}</span></td>
                                     <td>{{ $is_w }}</td>
                                     <td>{{ date('F d, Y', strtotime($repair->created_at))  }}</td>
                                     <td>
                                        <a href="{{ route('repairs.show', $repair->id)}}">Show Info</a>
                                     </td> 
                                 </tr>
                              @endforeach
                        </tbody>
                     </table>
                    </div> 
                </div>    
               @else
                  <p>No repair has been created.</p>
               @endif

        </div>
    </div>    
</section>
@endsection


@section('css')
<style>
.repair-log {
  max-height: 400px;
  overflow-y: scroll;
}
.list-of-options li {
    display: inline-block;
}
h4.label.label-success {
    font-size: 15px;
}
  .float-right {
    float: right;
  }
  .comment {
    padding: 10px;
    border: 1px solid #f5f3f3;
    margin-bottom: 5px;
    background: #f7f6f6;
  }
  .comment-form {
      margin-top: 20px;
      border-top: 1px solid #f3eded;
      padding-top: 10px;
  }
  .comment-form:after {
    content: '';
    clear: both;
    display: block;
  }
  .comment-form button[type="submit"] {
    border-bottom: 2px solid #2b8e2b !important;
    height: 30px;
    line-height: 1;
  }
  .comment .desc {
    font-weight: 600;
    color: #333;
  }
  h3.heading-rma {
    margin: 0;
    padding: 14px 10px;
    background: #2d2d2d;
    color: #fff;
    font-family: 'verdana','sans-serif';
    font-weight: 600;
    font-size: 16px;
}
.comment .date_created {
    color: #c5c4c4;
}
.form {
}
.form label {
    border-bottom: 1px solid #f1efef;
    color: #222;
    font-weight: 600;
    display: block;
}
.history-section .panel-body {
    height: 400px;
    overflow-y: scroll;
}
.form-group {
    margin-bottom: 15px;
    padding: 5px 0 15px;
    border-bottom: 1px solid #f9f5f5;
}
.btn-default {
  background: #f5f5f5;
  padding: 5px;
  line-height: 1;
  border-radius: 4px;
  display: inline-block;
}
.btn-default:hover {
  background-color: inherit;
}
.btn-open {
    background: #fb9210;
    color: #000;
}
.btn-ps {
  background: #004181;
  color: #fff;
}
.btn-cs {
    background: #2ba01c;
    color: #fff;
}
.btn-r {
    background: #dc3030;
    color: #fff;
}
.btn-rp {
    background: #ebef1a;
    color: #000;
}
.btn-rt {
    background: #ae68d2;
    color: #fff;
}
label {
  display: block;
} 
.form-group p {
  min-height: 20px;
}
</style>
@stop