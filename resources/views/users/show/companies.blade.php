

@if($userCompanies) 

   <div class="company-list">

       @foreach($userCompanies as $item)
         <div class="single-company" style="margin-bottom:30px;padding-bottom: 30px;border-bottom: 1px solid #ddd;">
               <div class="row">
                  <div class="col-sm-4">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Company Name</label>
                        <p>{{ optional($item->company)->name }}</p>
                      </div>
                  </div>
                  <div class="col-sm-8">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Description</label>
                        <p>{{ optional($item->company)->description }}</p>
                      </div>
                  </div>
               </div>   
               <div class="row">
                  <div class="col-sm-4">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Country</label>
                        <p>{{ optional($item->company)->country }}</p>
                      </div>
                  </div>
                  <div class="col-sm-8">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Address</label>
                        <p>{{ optional($item->company)->address }}</p>
                      </div>
                  </div>
               </div>   
               <div class="row">
                  <div class="col-sm-4">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Contact Number</label>
                        <p>{{ optional($item->company)->telephone_no }}</p>
                      </div>
                  </div>
                  <div class="col-sm-4">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Currency</label>
                        <p>{{ optional($item->company)->currency }}</p>
                      </div>
                  </div>
                  <div class="col-sm-4">
                     <div class="field field-group clearfix">
                        <label for="input_cn">Contact Person</label>
                        <p>{{ optional($item->company)->contact_person }}</p>
                      </div>
                  </div>
               </div>

         </div>


       @endforeach

  </div>  

@else

   <p>No company has been added.</p>

@endif

