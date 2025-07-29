
<div class="edit-item-popup hide">
    <div class="popup-block">
      <div class="loading"><div><img src="{{ asset('images/loading.gif') }}" /></div></div>
      <div class="popup-heading"><span>Edit Item</span><button id="btn-close">x</button></div>
      <div class="popup-body">

        <div class="row">
            <div class="col-md-12 {{ ($isEditor == true) ? 'hide' : '' }}">
                    <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
                        <label for="input_sn" class="col-sm-4 col-form-label">Serial Number</label>
                        <div class="col-sm-8">
                          <input class="required" type="text" name="serial_no" id="input_sn" value="{{ old('serial_no') }}">
                          <input class="required" type="hidden" name="rma_id" id="rmaId" value="">
                          <span class="validate-feedback">
                            This field is required
                          </span></div>
                    </div>
                    <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
                      <label for="date_purchase_known" class="col-sm-4 col-form-label">Date Purchase Known?</label>
                      <div class="col-sm-8"><input type="checkbox" name="date_purchase_known" id="date_purchase_known"> If tick, enter the date of purchased below
                        <input type="date" class="form-control" name="original_order_date" id="original_order_date" /></div>
                    </div>

                    <input type="hidden" name="user_id" id="userID" />
                    <input type="hidden" name="company_id" id="companyID" />

                    @if ($products)
                        <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                          <label for="input_pn" class="col-sm-4 col-form-label">Model</label>
                          <div class="col-sm-8">
                            <div id="input_pn" style="max-width: 100%;"></div>
                            {{-- <select class="required" id="input_pn" name="product">
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
                            <select id="selectIssue" class="selectedFaults required" name="issue"  style="margin-bottom: 0;min-height:150px;" multiple>
                              <option value=""></option>
                              @foreach ($issues as $issue)
                                  <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                              @endforeach 
                            </select>
                            <span class="validate-feedback">
                              This field is required
                            </span>
                            <span class="text-sm text-dark text-fault-note" style="font-size:13px;display:block;margin-bottom:20px;">Hold CTRL Key and Left click to select multiple faults</span>
                          
                           {{-- customer comment --}}
                            <div class="form-group">
                              <strong>Please specify additional comment (Client's fault description)</strong>
                                <div><textarea id="fault-comment" name="fault_described" value=""></textarea></div>
                                <span class="validate-feedback">
                                  This field is required
                                </span>
                            </div>
                          </div>
                        </div>
                    @endif

                    <div class="form-group row {{ $errors->has('invalid_serial') ? ' has-error' : '' }}">
                      <label for="invalid_serial" class="col-sm-4 col-form-label">Invalid Serial Number Flag</label>
                      <div class="col-sm-8">
                        <input type="checkbox" name="invalid_serial" id="invalid_serial">
                        {{-- <div class="yesno-switch-cover">
                          <div id="button-3" class="button r">
                            <input type="checkbox" class="input-checkbox checkbox" name="invalid_serial" id="invalid_serial">
                            <div class="knobs"></div>
                            <div class="layer"></div>
                          </div>
                        </div> --}}
                        {{-- <input type="checkbox" class="input-checkbox" name="invalid_serial" id="invalid_serial"> --}}
                      </div>
                    </div>
                    <div class="form-group row {{ $errors->has('warranty_flag') ? ' has-error' : '' }}">
                      <label for="warranty_flag" class="col-sm-4 col-form-label">Under Warranty</label>
                      <div class="col-sm-8">
                        <input type="checkbox" name="warranty_flag" id="warranty_flag"> <label>Yes</label>
                        <br />
                        <input type="checkbox" name="warranty_flag" id="warranty_flag_no"> <label>No</label>
                        {{-- <div class="yesno-switch-cover">
                          <div id="button-3" class="button r">
                            <input type="checkbox" class="input-checkbox checkbox" name="warranty_flag" id="warranty_flag">>
                            <div class="knobs"></div>
                            <div class="layer"></div>
                          </div>
                        </div> --}}
                        {{-- <input type="checkbox"  class="input-checkbox" name="warranty_flag" id="warranty_flag"> --}}
                      </div>
                    </div>

                    <div class="problemDesc hide">  
                      <div class="form-group{{ $errors->has('problem_description') ? ' has-error' : '' }}">
                        <label for="input-pd">Problem Description</label>
                        <textarea class="form-control" name="problem_description"  id="input-pd" rows="3"></textarea>
                      </div>
                    </div>
                    <div class="form-group row hide {{ $errors->has('original_order_date') ? ' has-error' : '' }}">
                      <label for="original_order_date" class="col-sm-4 col-form-label">Original Order Date</label>
                      <div class="col-sm-8"><input type="date" class="form-control" name="original_order_date" id="original_order_date" value=""></div>
                    </div>
                    <div class="form-group row">
                      <label for="repair_cost" class="col-sm-4 col-form-label">Repair Cost</label>
                      <div class="col-sm-8">
                          <input type="number" name="repair_cost" id="repair_cost" class="form-control">
                          <input type="hidden" name="item_id" id="item_id" class="form-control">
                      </div>
                    </div>
              </div>
             
              @if($isEditor)
              
                  <div class="col-md-12">
                    <div class="form-group row" >
                      <label for="root_cause_analysis" class="col-sm-4 col-form-label">Root Cause Analysis</label>
                      <div class="col-sm-8">
                        <select  id="root_cause_analysis" type="text" name="rootcause" value="">
                          <option value="">Select</option>
          
                          @foreach ($rootcause as $cause )
                            <option value="{{ $cause->name }}">{{ $cause->name }}</option>
                          @endforeach
                
                        </select>
                      </div>
                    </div>
                    <div class="form-group row pacom_fault_desc-wrap">
                      @php 
                        $pacom_faults = ['Smoke/Burn Smell', 'Serial UART Problem', 'Open-Collector Output Problem', 'EMC related problem', 'EEPROM Problem', 'CRI Reader Problem',
                        'Display Problem','Manufactoring Problem','Board Restart','Alarm Input Problem', 'Processor Problem', 'Battery Failure', 'RS485 Communication Failure',
                        'No Problem Found', 'Ethernet problem', 'Board doesn\'t Power-up', 'Other'];
                      @endphp
                      <label for="pacom_fault_desc" class="col-sm-4 col-form-label">Pacom Fault Description</label>
                      <div class="col-sm-8">
                        <select  name="pacom_fault_desc" id="pacom_fault_desc">
                          <option value="">Select</option>
                          @foreach ($pacom_faults as $pfault )
                            <option value="{{ $pfault }}" >{{ $pfault }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="pacom_fault_comment" class="col-sm-4 col-form-label">Pacom Comment</label>
                      <div class="col-sm-8">
                        <textarea name="pacom_fault_comment" id="pacom_fault_comment"></textarea>
                      </div>
                    </div>
              
                      <div class="form-group row mt-10">
                        <label for="status" class="col-sm-4 col-form-label">Notes</label>
                        <div class="col-sm-8">
                          <select  id="status" type="text" name="status" value="">
                            <option value="">Select</option>
            
                            @foreach ($itemstatus as $status )
                              <option value="{{ $status->name }}">{{ $status->name }}</option>
                            @endforeach
                  
                          </select>
                        </div>
                      </div>
              
                  <div class="form-group row {{ $errors->has('repaired_date') ? ' has-error' : '' }}">
                    <label for="repaired_date" class="col-sm-4 col-form-label">Repaired Date</label>
                    <div class="col-sm-8"><input type="date" class="form-control" name="repaired_date" id="repaired_date" value=""></div>
                  </div>
                  
                </div>  
                @endif 

                
              
              <div class="form-group row ">
                <div class="col-md-12">
                  <div class="col-sm-12" style="display:flex;justify-content:flex-end;"><button  class="btn btn-primary" id="updateButton">Update Fault Item</span></button></div>
                </div>
                
              </div>


            </div>

     
      </div><!-- Pop up heading End --> 
    </div><!-- Pop up block End --> 
  </div><!-- Add item End --> 
  <style>
    .validate-feedback {
      display: none;
    }
    .validate-error select, .validate-error input {
        border: 1px solid red;
    }  
    input#original_order_date {
        margin-top: 10px;
    }
    .edit-item-popup .loading {
        width: 100%;
        height: 100%;
        position: absolute;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.1);
        display: none;
    }
    .edit-item-popup .loading > div {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 999;
    }
    .edit-item-popup .loading img {
        width: 100px;
    }
    .edit-item-popup .validate-error  #fault-comment {
      border-color: red;
    }
    </style> 