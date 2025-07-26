<div class="item-list">
    <div class="heading"><h4>Faulty Items:</h4></div>
     <button id="addItem">Add Item</button>
     <table id="table-product" class="table table-stripe">
        <thead>
          <tr>
            <th width="200">Serial No</th>
            <th width="200">Model</th>
            <th width="250">Fault</th>
            <th width="150">Repair Cost</th>
            <th width="150">Order Date</th>
            <th width="70">Warranty</th>
            <th width="150">Action</th>
         </tr>
        </thead>
        <tbody></tbody>
     </table>
       
        <input type="hidden" name="status"  value="open" />
        <div class="hide loading"><div><img src="{{ asset('public/images/loading.gif') }}" /></div></div>
         
        <div class="form-btn-wrap">
          <button type="submit" id="submitRepair" class="btn-brand btn-brand-icon btn-brand-primary btn-main">Submit Request</button>
        </div>
      </div> <!-- Item List End --> 

  </div> 