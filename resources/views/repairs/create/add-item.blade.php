<div class="add-item-popup admin hide">
    <div class="popup-block">
      <div class="popup-heading"><span>Faulty Item</span><button id="btn-close">x</button></div>
      <div class="popup-body">
         <div class="row">
          <div class="col-md-6">
              <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
                <label for="input_sn" class="col-sm-4 col-form-label">Serial Number <span class="text text-danger">*</span></label>
                <div class="col-sm-8">
                  <input type="text" name="serial_no" id="input_sn" value="{{ old('serial_no') }}">
                  <div class="serial-number-note"><small>Please see below serial number examples. Put "NA" if serial number is not available.</small></div>
                    <div class="sn-example">
                      <p>Pacom's serial number example: 077G-2332-004500</p>
                      <p>SPG's serial number example: 2326-026H000132</p>
                      <a  href="{{ url('serial-no-examples') }}" target="_blank"><span> Click here to see the screenshots</span></a>
                    </div>
                </div>
            </div>

            <input type="hidden" name="user_id" id="userID" />
            <input type="hidden" name="company_id" id="companyID" value="" />

            @if ($products)
                <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                  <label for="input_pn" class="col-sm-4 col-form-label">Model <span class="text text-danger">*</span></label>
                  <div class="col-sm-8">
                    <div id="input_pn" style="max-width: 100%;"></div>
                    {{-- <select  id="input_pn" name="product">
                      <option value="">Select</option>

                      @foreach ($products as $product)
                        <option value="{{ $product->name }}">{{ $product->name }}</option>
                      @endforeach  

                  </select> --}}
                </div>
                </div>
            @endif
      

            @if ($issues) 
                <div class="form-group row {{ $errors->has('issue') ? ' has-error' : '' }}">
                  <label for="input_i"  class="col-sm-4 col-form-label">Fault Category <span class="text text-danger">*</span></label>
                  <div class="col-sm-8">
                    <select id="selectIssue" name="issue" multiple>
                      @foreach ($issues as $issue)
                          <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                      @endforeach 
                    </select><span class="text-sm text-dark text-fault-note">Hold CTR Key andf Left click to select multiple faults</span>
                    <div class="form-group"><strong>Please specify additional comment</strong>
                        <div><textarea id="fault-comment"></textarea></div>
                        <span class="validate-feedback">
                          This field is required
                        </span>
                    </div>
                  </div>
                </div>
            @endif
          </div>

            <div class="col-md-6">
              <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
                <label for="date_purchase_known" class="col-sm-4 col-form-label">Date Purchase Known?</label>
                <div class="col-sm-8">
                  <div><input type="checkbox" name="date_purchase_known" id="date_purchase_known"> If tick, enter the date of purchase below
                  <input type="date" class="form-control" name="original_order_date" id="original_order_date" value=""></div>
                </div>
              </div>
                <div class="form-group row {{ $errors->has('invalid_serial') ? ' has-error' : '' }}">
                  <label for="invalid_serial" class="col-sm-4 col-form-label">Invalid Serial Number Flag</label>
                  <div class="col-sm-8"><input type="checkbox" class="input-checkbox" name="invalid_serial" id="invalid_serial"></div>
                </div>
                <div class="form-group row {{ $errors->has('warranty_flag') ? ' has-error' : '' }}">
                  <label for="warranty_flag" class="col-sm-4 col-form-label">Under Warranty</label>
                  <div class="col-sm-8">
                    <div class="yesno-switch-cover">
                        <span><input type="radio"  class="input-checkbox warranty-option" name="warranty_flag" value="0"  id="warranty_flag"> No</span>
                        <span><input type="radio"  class="input-checkbox " name="warranty_flag" value="1" id="warranty_flag"> Yes</span>
                    </div>
              
                  </div>
                </div>

                <div class="form-group row">
                  <label for="repair_cost" class="col-sm-4 col-form-label">Repair Cost</label>
                  <div class="col-sm-8"><input type="number" name="repair_cost" id="repair_cost" class="form-control"></div>
                </div>
                <input type="hidden" name="itemIndex" id="itemIndex" value="" />
                <input type="hidden" name="isEditing" id="isEditing" value="0" />
            </div>
            <div class="col-md-12">
              <div class="btn-wrap">
                <button class="btn-brand btn-brand-icon btn-brand-primary" id="addProduct"><i class="fa fa-check"></i><span>Add Item</span></button></div>                     
            </div>
            {{-- <div class="form-group row ">
              <label for="input_sn" class="col-sm-4 col-form-label"></label>
              <div class="col-sm-8">
                <button  class="btn-brand btn-brand-icon btn-brand-primary" id="addProduct"><i class="fa fa-check"></i><span>Add Item</span></button></div>
            </div> --}}
         </div>

     
      </div><!-- Pop up heading End --> 
    </div><!-- Pop up block End --> 
  </div><!-- Add item End --> 