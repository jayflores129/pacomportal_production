@if(Auth::user())

  <h4 class="heading"><span class="inner">Menu</span>  <button id="toggle-menu"><span class="fa fa-bars"></span></button></h4>
    
  <ul class="vertical-menu">
      <li class="menu-item {{ Request::path() == 'home' ? 'active show-submenu' : '' }}">
         <a href="{{ url('/home') }}">
            <i class="fa fa-home fa-lg"></i>
           <span>Dashboard</span>
         </a>
      </li>
      <li class="menu-item {{ Request::path() == 'firmwares' ? 'active show-submenu' : '' }}">
         <a href="{{ url('/firmwares') }}">
            <i class="fa fa-file-code-o fa-lg"></i>
           <span>Software / Firmware</span>
         </a>
      </li>
      <li class="menu-item {{ Request::path() == 'technical-documentation' ? 'active show-submenu' : '' }}">
          <a href="{{ url('/technical-documentation') }}">
              <i class="fa fa-file-text-o fa-lg"></i>
              <span>Technical Documentation</span>
          </a>
      </li>
      <li class="menu-item {{ Request::path() == 'certificates' ? 'active show-submenu' : '' }}">
          <a href="{{ url('/certificates') }}">
               <i class="fa fa-certificate fa-lg"></i>
              <span>Certificates</span>
          </a>
      </li>
      <li class="menu-item parent-item {{ Request::path() == 'repairs' || Route::is('repairs.*') || Request::path() == 'rma/create' || Request::path() == 'repairs/issues' || Request::path() == 'repairs/create-issue' ? 'active show-submenu' : '' }}">
          <a href="{{ url('/repairs') }}"> 
              <i class="fa fa-wrench fa-lg"></i>
              <span>RMA</span>
              <i class="fa fa-chevron-left menu-dropdown-icon"></i>
          </a>
          <ul class="sub-menu">
              <li><a  href="{{ url('repairs') }}"><i>&rarr;</i><span>All RMA</span></a></li>
              @if(Auth::user()->isAdmin())
                <li><a  href="{{ url('rma/create') }}"><i>&rarr;</i><span>Add RMA</span></a></li>
              @else 
                <li><a  href="{{ url('rma/create-rma') }}"><i>&rarr;</i><span>Add RMA</span></a></li>
              @endif
          </ul>
      </li>
      <li class="menu-item parent-item {{ Request::path() == 'admin/softwares' || Request::path() == 'admin/softwares/create' ? 'active show-submenu' : '' }}">
          <a href="{{ url('/admin/softwares') }}"> 
              <i class="fa fa-bug fa-lg"></i>
              <span>Tasks</span>
              <i class="fa fa-chevron-left menu-dropdown-icon"></i>
          </a>
          <ul class="sub-menu">
              <li><a  href="{{ url('admin/softwares') }}"><i>&rarr;</i><span>All Tasks</span></a></li>
              <li><a  href="{{ url('admin/softwares/create') }}"><i>&rarr;</i><span>Add Task</span></a></li>
              <li><a  href="{{ url('admin/resolved-issues') }}"><i>&rarr;</i><span>All Resolved Tasks</span></a></li>
          </ul>
      </li>

  </ul>
 @endif
 @if(Auth::user() && Auth::user()->hasRole('admin'))
    <h4 class="heading"><span class="inner">Settings</span> </h4>
    <ul class="vertical-menu">
        <li class="menu-item parent-item {{ Request::path() == 'admin/users' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/users') }}"><i class="fa fa-key fa-lg"></i> <span>Manage Users</span>  <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>

            <ul class="sub-menu">
              <li><a  href="{{ url('admin/users') }}"><i>&rarr;</i><span>All Users</span></a></li>
              <li><a  href="{{ url('admin/users/create') }}"><i>&rarr;</i><span>Add User</span></a></li>
              {{-- <li><a  href="{{ url('admin/change-permission') }}"><i>&rarr;</i><span>Update Permission</span></a></li> --}}
              {{-- <li><a  href="{{ url('admin/for-approval') }}"><i>&rarr;</i><span>For Approval</span></a></li> --}}
              <li><a  href="{{ url('your-profile') }}"><i>&rarr;</i><span>My Profile</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/companies' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/companies') }}"><i class="fa fa-building-o fa-lg"></i> <span>All Companies</span>  <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>

            <ul class="sub-menu">
              <li><a  href="{{ url('admin/companies') }}"><i>&rarr;</i><span>All Companies</span></a></li>
              <li><a  href="{{ url('admin/companies/create') }}"><i>&rarr;</i><span>Add Company</span></a></li>
            </ul>
        </li>
        <li class="menu-item  {{ Request::path() == 'monitoring' ? 'active show-submenu' : '' }}">
          <a  href="{{ url('admin/monitoring') }}"><i class="fa fa-tv fa-lg"></i><span>Monitoring</span></a>
        </li>
        <li class="menu-item  {{ Request::path() == 'your-profile' ? 'active show-submenu' : '' }}">
          <a  href="{{ url('your-profile') }}"><i class="fa fa-user fa-lg"></i><span>My Profile</span></a>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/campaign' ? 'active show-submenu' : '' }}">
          <a  href="{{ url('admin/campaign') }}"><i class="fa fa-envelope-o fa-lg"></i><span>Subscribers</span>  <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/campaign') }}"><i>&rarr;</i><span>Subscribers</span></a></li>
            <li><a  href="{{ url('admin/unsubscribe') }}"><i>&rarr;</i><span>Unsubscribe</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/issues' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/issues') }}"><i class="fa fa-exclamation-circle fa-lg"></i> <span>Fault Categories</span> <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/issues') }}"><i>&rarr;</i><span>All Fault Categoies</span></a></li>
            <li><a  href="{{ url('admin/issues/create') }}"><i>&rarr;</i><span>Add A Fault Category</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/products' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/products') }}"><i class="fa fa-gittip fa-lg"></i> <span>Models</span> <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/products') }}"><i>&rarr;</i><span>All Models</span></a></li>
            <li><a  href="{{ url('admin/products/create') }}"><i>&rarr;</i><span>Add Model</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/rootcause' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/rootcause') }}"><i class="fa fa-exclamation fa-lg"></i> <span>Root Causes</span> <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/rootcause') }}"><i>&rarr;</i><span>All Root Causes</span></a></li>
            <li><a  href="{{ url('admin/rootcause/create') }}"><i>&rarr;</i><span>Add Root Cause</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/itemstatus' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/itemstatus') }}"><i class="fa fa-bug fa-lg"></i> <span>RMA Notes</span> <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/itemstatus') }}"><i>&rarr;</i><span>All Notes</span></a></li>
            <li><a  href="{{ url('admin/itemstatus/create') }}"><i>&rarr;</i><span>Add RMA Notes</span></a></li>
          </ul>
        </li>
        <li class="menu-item parent-item {{ Request::path() == 'admin/settings' ? 'active show-submenu' : '' }}">
          <a href="{{ url('admin/settings') }}"><i class="fa fa-gear fa-lg"></i> <span>Settings</span> <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('admin/settings') }}"><i>&rarr;</i><span>General</span></a></li>
            <li><a  href="{{ url('admin/email-setting') }}"><i>&rarr;</i><span>Email</span></a></li>
            <li><a  href="{{ url('admin/setting-api') }}"><i>&rarr;</i><span>Api</span></a></li>
            <li><a  href="{{ url('admin/password-change') }}"><i>&rarr;</i><span>Change Password</span></a></li>
          </ul>
        </li>
    </ul>  
 @elseif( Auth::user() && Auth::user()->hasRole('customer') )  
    <?php $id = Auth::user()->id; ?>
    <h4 class="heading"><span class="inner">Settings</span> </h4>
    <ul class="vertical-menu">
        <li class="menu-item parent-item {{ Request::path() == 'admin/customers' ? 'active' : '' }}">
          <a href="{{ url('/profile/') }}"><i class="fa fa-user fa-lg"></i> <span>Your profile</span>  <i class="fa fa-chevron-left menu-dropdown-icon"></i></a>
          <ul class="sub-menu">
            <li><a  href="{{ url('/profile') }}"><i>&rarr;</i><span>View Profile</span></a></li>
            <li><a  href="{{ url('/profile/') }}/{{$id}}/edit"><i>&rarr;</i><span>Edit Profile</span></a></li>
            <li><a  href="{{ url('/subscription') }}"><i>&rarr;</i><span>Subscription</span></a></li>
            <li><a  href="{{ url('admin/password-change') }}"><i>&rarr;</i><span>Change Password</span></a></li>
          </ul>
        </li>
    </ul> 
 @endif   



