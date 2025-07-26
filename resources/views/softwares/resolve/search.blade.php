
  <div class="advanced-search hide">
      <div class="panel panel-default repair-tab top-search">
      {{--  <div class="panel-header">
                 <h3 class="heading">Advanced Search</h3>
              </div> --}}
        <div class="panel-body">

            <button id="closeAdvanced"><span class="fa fa-close"></span></button>
              <?php $link = ( Auth::user()->isAdmin() ) ? 'advanced-search-resolve-task' : 'advanced-search-user-resolve-task'; ?>
              {!! Form::open(['method' => 'GET', 'route' => $link])  !!}
              <div class="row">
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="companyName">Ticket #</label>
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
                        <option value="Feature" {{ (Request('type') == 'Feature' ) ? 'selected':'' }}>Feature</option>
                        <option value="Defect" {{ (Request('type') == 'Defect' ) ? 'selected':'' }}>Defect</option>
                        <option value="Request" {{ (Request('type') == 'Task' ) ? 'selected':'' }}>Task</option>
                    </select>
                  </div>
                </div>
                {{-- <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Status</label>
                    <select type="text" name="status">
                        <option value="">Select Status</option>
                        <option value="To Do" {{ (Request('status') == 'To Do' ) ? 'selected':'' }}>To Do</option>
                        <option value="In Progress" {{ (Request('status') == 'In Progress' ) ? 'selected':'' }}>In Progress</option>
                        <option value="Completed" {{ (Request('status') == 'Completed' ) ? 'selected':'' }}>Completed</option>
                    </select>
                    <input type="hidden" name="status" value="resolved"/>
                  </div>
                </div> --}}
                <input type="hidden" name="status" value="resolved"/>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Keywords</label>
         
                     <input type="text" name="keywords" value="{{ Request('keywords')  }}" />
                  </div>
                </div>
              <input type="hidden" name="view" id="inputView" value="" /> 
                <div class="col-sm-12">
                  <div class="filter">
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary btn-brand-padding">Submit</button>
                  </div>
                </div>
              </div>
              {!! Form::close() !!}

        </div>
      </div>  
    </div>