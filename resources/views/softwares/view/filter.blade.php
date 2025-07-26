  <div class="filter-section">
      <div class="grid justify-space-between">
        <div class="col">
            <div class="filter filter-list">
              <span class="filter__label hidden-600">Quick Filters</span>
               <ul>
                 <li><a href="{{ url('admin/softwares') }}{{ ( Request('view') === 'grid' ) ? '?view=grid': '' }}" class="{{ ( Request::path() == 'admin/softwares' || Request::path() == 'advanced-search-task' ) ? 'active' : '' }}">All Tasks</a></li>
                 <li><a href="{{ url('admin/user-task') }}{{ ( Request('view') === 'grid' ) ? '?view=grid': '' }}" class="{{ Request::path() == 'admin/user-task' ? 'active' : '' }}">Assigned to Me</a></li>
                 <li><a href="{{ url('admin/task-submitted') }}{{ ( Request('view') === 'grid' ) ? '?view=grid': '' }} " class="{{ Request::path() == 'admin/task-submitted' ? 'active' : '' }}">Tasks Submitted</a></li>
               </ul>
             </div> 
        </div>
        <div class="col justify-content-end">
            <div class="filter-view">
               <span  class="filter__label ">Switch View</span>
               <ul>
                 <li {{ ($view !== 'grid') ? 'class=active':''}}><a href="{{ url(Request::path()) }}?view=list{{ ( Request('ticket_no') ) ? '&ticket_no=' . Request('ticket_no') : ''}}{{ ( Request('product') ) ? '&product=' . Request('product') : '' }}{{ ( Request('type') ) ? '&type=' . Request('type') : '' }}{{ ( Request('status') ) ?  '&status=' . Request('status') : '' }}{{ ( Request('keywords') ) ? '&keywords=' . Request('keywords') : '' }}"><span class="fa fa-th-list"></span> </a></li>
                 <li {{ ($view === 'grid') ? 'class=active': ''}} ><a href="{{ url(Request::path()) }}?view=grid{{ ( Request('ticket_no') ) ? '&ticket_no=' . Request('ticket_no') : ''}}{{ ( Request('product') ) ? '&product=' . Request('product') : '' }}{{ ( Request('type') ) ? '&type=' . Request('type') : '' }}{{ ( Request('status') ) ?  '&status=' . Request('status') : '' }}{{ ( Request('keywords') ) ? '&keywords=' . Request('keywords') : '' }}"><span class="fa fa-th-large"></span></a></li>
               </ul>
             </div>
        </div>
      </div>
  </div>

