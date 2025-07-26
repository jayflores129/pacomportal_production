
<div class="add-item-popup hide">
    <div class="popup-block">
      <div class="loading"><div><img src="{{ asset('images/loading.gif') }}" /></div></div>
      <div class="popup-heading"><span>Add Item</span><button id="btn-add-close">x</button></div>
      <div class="popup-body">
   
          <!-- @if( Auth::user()->hasRole('customer') )

            <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
              <input type="hidden" class="form-control" id="searchCompany" name="company" value="{{ Auth::user()->id }}" />
            </div>

          @else
            <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                <label for="input_cn">Search Company</label>
                <input type="text" class="form-control" id="searchCompany" name="company"  />
                <div id="searchResults"></div> 
                <input type="hidden" name="company_id" id="companyID" />
            </div>

          @endif -->
          <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
              <label for="input_sn" class="col-sm-4 col-form-label">Serial Number</label>
              <div class="col-sm-8">
                <input class="required" type="text" name="serial_no" id="input_sn" value="{{ old('serial_no') }}">
                <span class="validate-feedback">
                  This field is required
                </span>
              </div>
          </div>
          

          <input type="hidden" name="user_id" id="userID" />
          <input type="hidden" name="company_id" id="companyID" />

          @if ($products)
              <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                <label for="input_pn" class="col-sm-4 col-form-label">Model</label>
                <div class="col-sm-8">
                  <div id="input_pn" style="max-width: 100%;"></div>
                  {{-- <select  id="input_pn" name="product" class="required">
                    <option value="">Select</option>

                    @foreach ($products as $product)
                      <option value="{{ $product->name }}">{{ $product->name }}</option>
                    @endforeach  
                   
                </select> --}}
              <span class="validate-feedback">
                  This field is required
                </span></div>
              </div>
          @endif
    

          @if ($issues) 
              <div class="form-group row {{ $errors->has('issue') ? ' has-error' : '' }}">
                <label for="input_i"  class="col-sm-4 col-form-label">Fault Category</label>
                <div class="col-sm-8">
                  
                    <select id="selectIssue" name="issue" multiple class="required field-with-sub">
                      @foreach ($issues as $issue)
                          <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                      @endforeach 
                    </select>
                    <span class="text-sm text-dark text-fault-note sub-label">Hold CTRL Key and Left click to select multiple faults</span>
                    <span class="validate-feedback">
                      This field is required
                    </span>
                  <div class="b-g-15"></div> 
                  <div class="form-group">
                    <strong>Please specify additional comment</strong>
                      <div class="fault-block">
                        <textarea id="fault-comment"></textarea>
                        <span class="validate-feedback">
                          This field is required
                        </span>
                      </div>
                  </div>
                </div>
              </div>
          @endif

          <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
            <label for="date_purchase_known" class="col-sm-4 col-form-label">Date Purchase Known?</label>
            <div class="col-sm-8">
              <div><input type="checkbox" name="date_purchase_known" id="date_purchase_known">  If tick, enter the date of purchase below</div>
              <input type="date" class="form-control" name="original_order_date" id="original_order_date" value="">
            </div>
          </div>

          <div class="form-group row {{ $errors->has('invalid_serial') ? ' has-error' : '' }}">
            <label for="invalid_serial" class="col-sm-4 col-form-label">Invalid Serial Number Flag</label>
            <div class="col-sm-8">
              <input type="checkbox" name="invalid_serial" id="invalid_serial">
            </div>
          </div>
          {{-- <div class="form-group row {{ $errors->has('warranty_flag') ? ' has-error' : '' }}">
            <label for="warranty_flag" class="col-sm-4 col-form-label">Under Warranty</label>
            <div class="col-sm-8">
              <input type="checkbox" name="warranty_flag" id="warranty_flag"> <label>Yes</label> <br/>
              <input type="checkbox" name="warranty_flag_no" id="warranty_flag_no"> <label>No</label>
            </div>
          </div> --}}
          <div class="form-group row {{ $errors->has('warranty_flag') ? ' has-error' : '' }}">
            <label for="warranty_flag" class="col-sm-4 col-form-label">Under Warranty</label>
            <div class="col-sm-8">
              <div class="yesno-switch-cover">
                  <span><input type="radio"  class="input-checkbox warranty-option" name="warranty_flag" value="0"  id="warranty_flag"> No</span><br>
                  <span><input type="radio"  class="input-checkbox " name="warranty_flag" value="1" id="warranty_flag"> Yes</span>
              </div>
        
            </div>
          </div>

          <div class="problemDesc hide">  
            <div class="form-group{{ $errors->has('problem_description') ? ' has-error' : '' }}">
              <label for="input-pd">Problem Description</label>
              <textarea class="form-control" name="problem_description"  id="input-pd" rows="3"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="repair_cost" class="col-sm-4 col-form-label">Repair Cost</label>
            <div class="col-sm-8"><input type="number" name="repair_cost" id="repair_cost" class="form-control">
              </div>
            <input type="hidden" name="rma_id" id="rma_id" value="{{ $repair->id }}" class="form-control">
            <input type="hidden" name="isCustomer" id="isCustomer" value="no" />
          </div>
          <div class="form-group row ">
            <label for="input_sn" class="col-sm-4 col-form-label"></label>
            <div class="col-sm-8"><button  class="btn-brand btn-brand-icon btn-brand-primary" id="addItemButton"><i class="fa fa-check"></i><span>Add Item</span></button></div>
          </div>

     
      </div><!-- Pop up heading End --> 
    </div><!-- Pop up block End --> 
  </div><!-- Add item End --> 

  <style>
    span.validate-feedback {
      color: red;
  }
    .add-item-popup .validate-error  #fault-comment {
      border-color: red;
    }
  .validate-feedback {
    display: none;
  }
  .validate-error select, .validate-error input {
      border: 1px solid red;
  }  
  input#original_order_date {
      margin-top: 10px;
  }
  .add-item-popup .loading {
      width: 100%;
      height: 100%;
      position: absolute;
      z-index: 9999;
      background: rgba(0, 0, 0, 0.1);
      display: none;
  }
  .add-item-popup .loading > div {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 999;
  }
  .add-item-popup .loading img {
      width: 100px;
  }
  </style> 