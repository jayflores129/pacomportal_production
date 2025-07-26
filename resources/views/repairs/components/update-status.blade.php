<div class="add-status-popup popup-form hide">
    <div class="popup-block">
      <div class="popup-heading"><span>Update Status</span><button class="btn-add-close">x</button></div>
      <div class="popup-body">
        @if( Auth::user()->company == $repair->company || Auth::user()->isAdmin()  )

              {!! Form::open(['method' => 'post', 'route' => 'rma_status_update' ]) !!}
                 <input type="hidden" name="rma_id" value="{{ $repair->id }}"/>
                  <div class="form-group row">
                    {{-- <div class="col-md-12">
                        <label for="rma_status"  class="col-form-label">Status</label>
                        <select id="rma_status" name="rma_status" class="form-control"  required>
                          <option value="">Select</option>
                          {{-- <option value="Requested by ">Requested by</option>
                          <option value="Confirmed by ">Confirmed by</option> --}}
                          {{-- <option value="To be confirmed on">To be confirmed</option>
                          <option value="Items received on">Items received on</option>
                          <option value="Repair completed on">Repair completed on</option>
                          <option value="Goods have been shipped on">Goods have been shipped on</option>
                          <option value="RMA has been cancelled on">RMA has been cancelled on</option>
                        </select>
                    </div> --}} 
                    {{-- <div class="col-md-6">
                      <label for="rma_date"  class="col-form-label">Date</label>
                      <input type="date" name="rma_date" class="form-control" required/>
                    </div> --}}
                </div>
                <div class="row"  id="shipInfo">
                  <div class="col-md-6">
                    <label for="rma_courier"  class="col-form-label">Courier</label>
                    @php $couriers = ['DHL', 'UPS', 'FedEx',  'TNT' ] @endphp
                    <select  type="text" id="rma_courier" name="rma_courier" class="form-control" required>
                      @foreach ($couriers as $courier)
                          <option value="{{ $courier }}">{{ $courier }}</option>
                      @endforeach  
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label for="consignment_note"  class="col-form-label">Consignment Note</label>
                    <textarea id="consignment_note" class="form-control" name="consignment_note"></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12" style="margin-top:10px;margin-bottom:20px">
                    <button type="submit"  class="btn btn-primary"><span>Submit</span></button>
                  </div>
                </div>
                
                {!!  Form::close() !!}
              
           @endif   
        </div><!-- Pop up heading End --> 
    </div><!-- Pop up block End --> 
</div><!-- Add item End --> 


<script>
 
   $('#rma_status').on('change', function(){
        const status = $(this).val();

        if(status == 'To be confirmed on') {
            
        }
   })
</script>
