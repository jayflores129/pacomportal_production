<div class="grid justify-space-between grid-search-wrapper">
  <div class="col">
    {{-- <div class="rma-group">
        <div class="group-label">FILTER:</div>
   
        <div class="group-dropdown">
          {{-- @php $hasDefaultSearch = false @endphp
          @php $search_id =  app('request')->input('filter_id'); @endphp
          <select name="filter_group" id="groupFilter" type="text" class="form-control">
             @if($search_id == 0)
               <option value="0" selected>All</option>
            @else 
              <option value="0">All</option>
            @endif
              @foreach($searchGroup as $group)
                 
                 @if( $group->id == $defaultSearch && ($search_id != 0 || $search_id == ''))
                    <option value="{{ $group->id }}" selected>{{ $group->name }}</option>
                    @php $hasDefaultSearch = true @endphp
                 @else
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @php $hasDefaultSearch = false @endphp
                
                 @endif
  
              @endforeach
          </select> --}}
        
        {{-- </div>
        <div class="group-edit">
          @if(!$searchGroup->isEmpty() && $defaultSearch != '' || $hasDefaultSearch == true)
           <button id="groupEdit"><div class="edit-filter"><span class="fa fa-edit"></span> Edit Filter</div><div class="hide-filter hide"><span class="fa fa-hide"></span> Hide Filter</div></button>
          @endif 
        </div> 
    </div> --}}
  </div>
  <form class="col" action="/repairs" method="get">
    <div class="float-right">
      <div class="grid grid-search-wrapper">
        <div class="col q-search-box">
          {{-- <select name="filtered_by">
            <option value="rma_id" {{ request('filtered_by') == 'rma_id' ? 'selected' : '' }}>RMA ID</option>
            <option value="serial_number" {{ request('filtered_by') == 'serial_number' ? 'selected' : '' }}>Serial Number</option>
         </select> --}}
          <label for="search" class="hide"><span class="fa fa-search"></span></label>
          <span><input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Quick Search.."></span>
          
        </div>
          <div class="col"> 
              <ul class="list-inline">
                <li><button id="clearBtn" class="btn-brand btn-brand-icon btn-brand-danger" type="submit"><span class="fa fa-search"></span></button></li>
                <li><a href="{{ url('/search-rma') }}" id="createGroupx"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                  Advanced Search</span></a></li>
              </ul>  
           
          </div> 
        </div>
    </div>
  </form>
  {{-- <div class="col">
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
                <li><a href="{{ url('/search-rma') }}" id="createGroupx"  class="btn-brand btn-brand-icon btn-brand-primary"><i class="fa fa-filter"></i> <span>
                  Advanced Search</span></a></li>
              </ul>  
           
          </div> 
        </div>
    </div>
  </div> --}}
</div>
{{-- @include('repairs/components/search/new')
@include('repairs/components/search/edit') --}}

