
<div class="add-item-popup hide">
    <div class="popup-block">
      <div class="loading"><div><img src="{{ asset('public/images/loading.gif') }}" /></div></div>
      <div class="popup-heading"><span>Add Item</span><button id="btn-add-close">x</button></div>
      <div class="popup-body">

          <div class="form-group row {{ $errors->has('serial_no') ? ' has-error' : '' }}">
              <label for="input_sn" class="col-sm-4 col-form-label">Serial Number <span class="text text-danger">*</span></label>
              <div class="col-sm-8">
                <input class="required" type="text" name="serial_no" id="input_sn" value="{{ old('serial_no') }}">
                <span class="validate-feedback">
                  Field required
                </span>
                  <div class="serial-number-note"><small>Please see below serial number examples. Put "NA" if serial number is not available.</small></div>
                  <div class="sn-example">
                    <p>Pacom serial number example: 077G-2332-004500</p>
                    <p>SPG serial number example: 2326-026H000132</p>
                    <a  href="{{ url('serial-no-examples') }}" target="_blank"><i class="fa fa-info fa-sm"></i><span> Click here to see the screenshots</span></a>
                  </div>
              </div>
          </div>
          
          <input type="hidden" name="isCustomer" id="isCustomer" value="yes" />
          <input type="hidden" name="user_id" id="userID" />
          <input type="hidden" name="rma_id" id="rma_id" value="{{ $repair->id }}" />

          @if ($products)
              <div class="form-group row {{ $errors->has('product') ? ' has-error' : '' }}">
                <label for="input_pn" class="col-sm-4 col-form-label">Model <span class="text text-danger">*</span></label>
                <div class="col-sm-8">
                  <select  id="input_pn" name="product" class="required">
                    <option value="">Select</option>

                    @foreach ($products as $product)
                      <option value="{{ $product->name }}">{{ $product->name }}</option>
                    @endforeach  

                </select>
              <span class="validate-feedback">
                  Field required
                </span></div>
              </div>
          @endif
    

          @if ($issues) 
              <div class="form-group row {{ $errors->has('issue') ? ' has-error' : '' }}">
                <label for="input_i"  class="col-sm-4 col-form-label">Fault Category <span class="text text-danger">*</span></label>
                <div class="col-sm-8">
                  <select id="selectIssue" name="issue" multiple class="required">
                
                    @foreach ($issues as $issue)
                        <option value="{{ $issue->name }}">{{ $issue->name }}</option>
                    @endforeach 
                  </select>
                  <span class="validate-feedback">
                    Field required
                  </span>
                  <span class="text-sm text-dark text-fault-note">Hold CTR Key andf Left click to select multiple faults</span>
                  <div><strong>Please specify additional comment</strong>
                      <div class="fault-block"><textarea id="fault-comment"></textarea>
                        <span class="validate-feedback">
                          This field is required
                        </span>
                      </div>
                  </div>
                </div>
              </div>
          @endif

          {{-- <div class="form-group row {{ $errors->has('date_purchase_known') ? ' has-error' : '' }}">
            <label for="date_purchase_known" class="col-sm-4 col-form-label">Date Purchase Known?</label>
            {{-- <div class="col-sm-8"><input type="checkbox" name="date_purchase_known" id="date_purchase_known"></div>
            <div class="col-sm-8"><input type="checkbox" name="date_purchase_known" id="date_purchase_known">  If tick, enter the date of purchase below
            <input type="date" class="form-control" name="original_order_date" id="original_order_date" value=""></div>
          </div> --}}


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