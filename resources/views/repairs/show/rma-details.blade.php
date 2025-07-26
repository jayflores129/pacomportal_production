<div class="row">
    <div class="col-sm-6">
      <div class="row">
          <div class="col-sm-12">
            <div class="field field-group clearfix">
              <label for="input_cn">RMA #</label>
              <p>R{{ $repair->id }}</p>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="field field-group clearfix">
              <label for="input_cn">Date Requested</label>
              <p>{{ $repair->requested_date }}</p>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="field field-group clearfix">
              <label for="input_cn">PO Number</label>
              <p>{{ $repair->po_number }}</p>
            </div>
          </div> 
      </div>  
    </div>
    <div class="col-sm-6">

        <div class="field field-group clearfix">
          <label for="input-pd">Created By</label>
          <div><a href="{{ url('profile') }}/{{ $repair->user_id }}">{{ DB::table('users')->where('id', $repair->user_id)->value('firstname') }} {{ DB::table('users')->where('id', $repair->user_id)->value('lastname') }}</a></div>
        </div>

        {{-- <div class="field field-group clearfix">
          <label for="input-pd">Status</label>
          <p>{{ $repair->status }}</p>
        </div> --}}


        <div class="field field-group clearfix">
          <label for="input_cn">Currency</label>
          <p>{{ $repair->currency }}</p>
        </div>
        <div class="field field-group clearfix ">
          <label for="input_sn">Status</label>
          @if( Auth::user()->isAdmin()  )
           <div class="rma-field">
            <div class="status-view"><span class="{{ $repair->getBtnColor() }}" >{{ $repair->status }}</span><button id="updateStatus"><span class="fa fa-edit"></span></button></div>
            {{ Form::open(array('url' => '/update-repair-status/' . $repair->id , 'method' => 'PUT' )) }}
              <div class="group-field mt-2">
                <div class="inner-wrap">
                  <select name="selectStatus" id="selectStatus">
                    <option value="Open" {{ $repair->status == 'Open' ? 'selected' : '' }}>Open</option>
                    <option value="To Be Confirmed" {{ $repair->status == 'To Be Confirmed' ? 'selected' : '' }}>To Be Confirmed</option>
                    <option value="Confirmed" {{ $repair->status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="Received" {{ $repair->status == 'Received' ? 'selected' : '' }}>Received</option>
                    <option value="Completed" {{ $repair->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Shipped" {{ $repair->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="Cancelled" {{ $repair->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                  </select>
                  <button class="btn btn-primary">Update</button>
                  <button id="cancelEditStatus"><span class="fa fa-close"></span></button>
                </div>
              </div>
              {!! Form::close() !!}
            </div>
            @else
              <p>{{ $repair->status }}</p>
            @endif
        </div>
    </div>  

  </div>
  <br><br>
  <div class="row">
    <div class="item-list col-sm-6">
      <div class="heading"><h4>Requester:</h4></div>
      <div class="row">
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_pn">Name</label>
            <p>{{ $repair->requester_name }}</p>
          </div>
        </div>
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_sn">Phone</label>
            <p>{{ $repair->requester_phone}}</p>
          </div>
        </div> 
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_sn">Email</label>
            <div style="display: flex;"">
              <span>{{ $repair->requester_email}}</span>
              @if(Auth::user()->isAdmin())
                <div style="display: flex;align-items: center; gap: 5px;margin-left: auto;">
                  <label class="switch" style="width: 34px;float: unset;">
                    <input type="checkbox" name="notify" id="notify" {{ $repair->notify == 1 ? 'checked' : '' }}>
                    <span class="slider round"></span>
                  </label>
                  <span id="notify-text">Notify</span>
                </div>
              @endif
            </div>
          </div>
        </div> 
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_sn">Company</label>
            <p>{{ $repair->requester_company}}</p>
          </div>
        </div>
        <div class="col-sm-12"> 
          <div class="field field-group clearfix" style="margin-bottom: 30px;">
            <label for="input_sn">Fax</label>
            <p>{{ $repair->requester_fax}}</p>
          </div>
        </div>
      </div>  <!-- Requester Row --> 
    </div>
    <div class=" item-list col-sm-6">
      <div class="heading"><h4>Delivery Address:</h4></div>
      <div class="row">
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_sn">Company Name</label>
            <p>{{ $repair->company_name}}</p>
          </div>
        </div>
        <div class="col-sm-12"> 
          <div class="field field-group clearfix">
            <label for="input_sn">Phone</label>
            <p>{{ $repair->company_phone}}</p>
          </div>
        </div>
          <div class="col-sm-12"> 
            <div class="field field-group clearfix">
              <label for="input_sn">Fax</label>
              <p>{{ $repair->company_fax}}</p>
            </div>
          </div>
          <div class="col-sm-12"> 
            <div class="field field-group clearfix">
              <label for="input_sn">Country</label>
              <p>{{ $repair->country }}</p>
            </div>
          </div>
          <div class="col-sm-12"> 
            <div class="field field-group clearfix">
              <label for="input_sn">Address</label>
              <p>{{ $repair->company_address}}</p>
            </div>
          </div>
        </div>
    </div>
  </div>

 <br /><br />

 <script>
  document.getElementById('notify').addEventListener('click', async (e) => {
    const { checked } = e.target;

    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content')

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('status', checked ? 1 : 0)
    formData.append('rma_id', "{{ $repair->id }}")
    
    const res = await fetch("{{URL::to('/rmaSetNotification')}}", {
      method: 'post',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    const json = await res.json();

    if (json.success) {
      location.reload()
    }
  })
 </script>

 <style>
  .group-field {
    display: inline-block;
    /* width: 100%; */
}
.group-field .inner-wrap{
  display: flex;
}
.group-field select{
  width: 100%;
}
.field-group form{
  margin: 0
}
 </style>