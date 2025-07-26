
@if( $company->count() > 0 )

<div class="row">

   <div class="col-sm-4">

    <div class="field field-group clearfix">

      <label for="input_cn">Company Name</label>
      <p>{{ $company->name }}</p>

    </div>

  </div> 

  <div class="col-sm-4"> 

    <div class="field field-group clearfix">

      <label for="input_pn">Description</label>

      <p>{{ $company->description }}</p>

    </div>

  </div>

  <div class="col-sm-4"> 

    <div class="field field-group clearfix">

      <label for="input_sn">Country</label>

      <p>{{ $company->country }}</p>

    </div>

  </div> 

  <div class="col-sm-4"> 

    <div class="field field-group clearfix">

      <label for="input_sn">Address</label>

      <p>{{ $company->address }}</p>

    </div>

  </div> 

  <div class="col-sm-4"> 

    <div class="field field-group clearfix">

      <label for="input_i">Phone no</label>

      <p>{{ $company->telephone_no }}</p>

    </div>

  </div>

 
</div>  

 
@else
  
  Company : {{ $repair->company }}  

@endif