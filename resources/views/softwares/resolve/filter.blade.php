  

  <div class="grid justify-space-between grid-search-wrapper">
    <div class="col">
      <div class="grid grid-search-wrapper">
        <div class="col">
          <label for="search" class="hide">Quick Search</label>
          <span><input type="text" id="search" name="search" class="form-control" placeholder="Quick Search.."></span>
        </div>
        <div class="col"> 
            <ul class="list-inline">
              <li><button id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger"><span>Clear</span></button></li>
              <li><a href="#" id="advanced-search-button"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
         Advanced Search</span></a></li>
            </ul>  
        </div> 
      </div>
  </div>
    <div class="col">
        <div class="float-right">
          <span>Show Results 
            <?php $items = [ 15,25, 50, 100 ]; ?>
            @if($items)
              <select id="totalItems">
                 @foreach($items as $item)
                    <option value="{{ $item }}" <?php echo ( $totalitems == $item ) ? 'selected="selected"' : ''; ?>>{{ $item }}</option> 
                 @endforeach 
              </select>
            @endif
          </span>
      </div>
    </div>
  </div>