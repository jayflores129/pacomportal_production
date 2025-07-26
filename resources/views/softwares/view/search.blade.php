
  <div class="advanced-search hide">
      <div class="panel panel-default repair-tab top-search">
      {{--  <div class="panel-header">
                 <h3 class="heading">Advanced Search</h3>
              </div> --}}
        <div class="panel-body">

            <button id="closeAdvanced"><span class="fa fa-close"></span></button>
              <?php $link = ( Auth::user()->isAdmin() ) ? 'advanced-search-task' : 'advanced-search-user-task'; ?>
              <form action="/admin/softwares">
              <div class="row">
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="companyName">Ticket #  </label>
                    <input type="text" name="ticket_no" placeholder="" />
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairProduct">Product</label>
                    <?php $products = DB::table('products')->get() ?>
                    @if($products)
                       <select type="text" name="product">
                         <option value="">Select Product</option>
                         @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ (Request('product') == $product->id ) ? 'selected':'' }}>{{ $product->name }}</option>
                           @endforeach
                        </select>
                      @endif
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Type</label>
                    <select type="text" name="type">
                        <option value="">Select Type</option>
                        <option value="Feature" {{ (request('type') == 'Feature' ) ? 'selected':'' }}>Feature</option>
                        <option value="Defect" {{ (request('type') == 'Defect' ) ? 'selected':'' }}>Defect</option>
                        <option value="Request" {{ (request('type') == 'Task' ) ? 'selected':'' }}>Task</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Status</label>
                    <select type="text" name="status">
                        <option value="">Select Status</option>
                        <option value="To Do" {{ (request('status') == 'To Do' ) ? 'selected':'' }}>To Do</option>
                        <option value="In Progress" {{ (request('status') == 'In Progress' ) ? 'selected':'' }}>In Progress</option>
                        <option value="Completed" {{ (request('status') == 'Completed' ) ? 'selected':'' }}>Completed</option>
                        <option value="Resolved" {{ (request('status') == 'Resolved' ) ? 'selected':'' }}>Resolved</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Keywords</label>
         
                     <input type="text" name="keywords" value="{{ request('keywords')  }}" />
                  </div>
                </div>
              <input type="hidden" name="view" id="inputView" value="" /> 
                <div class="col-sm-12">
                  <div class="filter">
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary btn-brand-padding">Submit</button>
                  </div>
                </div>
              </div>
            </form>

        </div>
      </div>  
    </div>