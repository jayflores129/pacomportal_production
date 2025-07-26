
  <div class="advanced-search hide">
      <div class="panel panel-default repair-tab top-search">
      {{--  <div class="panel-header">
                 <h3 class="heading">Advanced Search</h3>
              </div> --}}
        <div class="panel-body">
          @if($repairs)
            <button id="closeAdvanced"><span class="fa fa-close"></span></button>
               <?php $link = ( Auth::user()->isAdmin() ) ? 'advanced-search' : 'search-customer-repair'; ?>
              {!! Form::open(['method' => 'GET', 'route' => $link])  !!}
              <div class="row">
                @if(Auth::user()->isAdmin())
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="companyName">Company</label>
                    <input type="text" name="company_name" placeholder="Company Name" value="{{ (Request('company_name') ) ? Request('company_name'): '' }}"/>
                    <input type="hidden" name="items" value="{{ (Request('items') ) ? Request('items'): '' }}" />
                  </div>
                </div>
                @endif
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairProduct">Product</label>
                    @if($products)
                       <select type="text" name="product">
                         <option value="">Select Product</option>
                         @foreach($products as $product)
                                <option value="{{ $product->name }}" {{ (Request('product') == $product->name ) ? 'selected':'' }}>{{ $product->name }}</option>
                           @endforeach
                        </select>
                      @endif
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairProduct">Issue</label>
                    @if($issues)
                       <select type="text" name="issue">
                         <option value="">Select issue</option>
                         @foreach($issues as $issue)
                                <option value="{{ $issue->name }}" {{ (Request('issue') == $issue->name ) ? 'selected':'' }}>{{ $issue->name }}</option>
                           @endforeach
                        </select>
                      @endif
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                    <label for="repairStatus">Status</label>
                    <?php   $status = ['open','Partially Shipped', 'Completly Shipped', 'received', 'repaired', 'returned']; ?>
                    <select type="text" name="status" class="text-capitalize">
                        <option value="">Select Status</option>
                        @foreach($status as $value)
                            <option value="{{ $value }}" {{ (Request('status') == $value) ? 'selected':'' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="filter">
                      <button type="submit" class="btn-brand btn-brand-icon btn-brand-primary btn-brand-padding">Submit</button>
                  </div>
                </div>
              </div>
              {!! Form::close() !!}
          @endif

        </div>
      </div>  
    </div>
  