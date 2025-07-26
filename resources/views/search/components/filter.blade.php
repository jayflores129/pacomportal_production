<div class="grid justify-space-between grid-search-wrapper">
    <div class="col">
    </div>
    <div class="col">
      <div class="float-right">
        <div class="grid grid-search-wrapper">
            <div class="col q-search-box">
              <select id="quick-search-type">
                <option value="rma_id">RMA ID</option>
                <option value="serial_number">Serial Number</option>
             </select>
              <label for="search" class="hide"><span class="fa fa-search"></span></label>
              <span><input type="text" id="search" name="search" class="form-control" placeholder="Quick Search.."></span>
              
            </div>
            <div class="col"> 
                <ul class="list-inline">
                  <li><button id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger"><span class="fa fa-search"></span></button></li>
                  <li><a href="{{ url('/search-rma') }}" id="advanced-search-buttonx"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
             Advanced Search</span></a></li>
                </ul>  
             
            </div> 
          </div>
      </div>
    </div>
  </div>
