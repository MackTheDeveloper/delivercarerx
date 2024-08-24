<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto"><a class="navbar-brand" href="">
          <div class="brand-logo">
            <img src="{{asset('assets/img/small-logo.png')}}" alt="avatar" class="logo" width="26px" height="26px" />
            <img src="{{asset('assets/img/small-logo.png')}}" alt="avatar"  class="logo" width="26px" height="26px"/>
          </div>
          <h2 class="brand-text mb-0">DeliverCare</h2>
        </a></li>
      <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
      <li class="active  nav-item">
        <a href="{{route('htmlpages','dashboard')}}">
          <i class="menu-livicon" data-icon="desktop"></i>
          <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard </span>
        </a>
      </li>

      <li class=" navigation-header text-truncate"><span data-i18n="User-Managment">DeliverCare   </span>
      </li>


      
      
      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="gears"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Role</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','role-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Role List</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- <li class=" nav-item"><a href="app-file-manager.html"><i class="menu-livicon" data-icon="save"></i><span class="menu-title text-truncate" data-i18n="File Manager">File Manager</span></a>
      </li> -->
      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">DeliverCare User</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','user-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">User List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','user-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">User Add</span>
            </a>
          </li>
        </ul>
      </li>


      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="building"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Pharmacy</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','pharmacy-list')}}">
            <a class="d-flex align-items-center" href="{{route('pharmacy_listing')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Pharmacy List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','pharmacy-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">Pharmacy Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" navigation-header text-truncate"><span data-i18n="User-Managment">Hospice Managment</span>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="building"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Hospice</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','hospice-list')}}">
            <a class="d-flex align-items-center" href="{{route('hospice-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Hospice List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','hospice-add')}}">
            <a class="d-flex align-items-center" href="{{route('show-hospice-form')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">Hospice Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Facility</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','facility-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Facility List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','facility-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">Facility Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Branch</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','branch-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Branch List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','branch-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">Branch Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Users</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','hospice-user-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">User List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','hospice-user-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">User Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span class="menu-title text-truncate" data-i18n="Nurse">Nurse</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('assign-nurse')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="Assign Nurse">Assign Nurse</span>
            </a>
          </li>

        </ul>
      </li>



      <li class="navigation-header text-truncate">
        <span data-i18n="Refill Management">Refill Management</span>
      </li>
      <li class="nav-item d-none">
        <a href="{{route('htmlpages','patients')}}">
          <i class="menu-livicon" data-icon="users"></i>
          <span class="menu-title text-truncate" data-i18n="Patients">Patients</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('htmlpages','refills-listing')}}">
          <i class="menu-livicon" data-icon="hourglass"></i>
          <span class="menu-title text-truncate" data-i18n="Refills">Refills</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('htmlpages','refills-in-queue')}}">
          <i class="menu-livicon" data-icon="priority-high"></i>
          <span class="menu-title text-truncate" data-i18n="Refills In-Queue">Refills In-Queue</span>
        </a>
      </li>
      <li class=" nav-item">
        <a>
          <i class="menu-livicon" data-icon="shoppingcart"></i>
          <span class="menu-title text-truncate" data-i18n="Orders">Orders</span>
        </a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','latest-orders')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Latest Orders</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','all-orders')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">All Orders</span>
            </a>
          </li>
        </ul>
      </li>


          
        </ul>
      </li>

      

      <li class=" navigation-header text-truncate"><span data-i18n="User-Managment">Shipping Carriers</span>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="truck"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Shipping Carriers</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','shipping-carrier-list')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Shipping Carrier List</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','shipping-carrier-add')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="View">Shipping Carrier Add</span>
            </a>
          </li>
        </ul>
      </li>

      <li class=" navigation-header text-truncate"><span data-i18n="User-Managment">Import</span>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="file-import"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Import</span></a>
        <ul class="menu-content">
          <li>
            <li>
              <a class="d-flex align-items-center" href="{{route('htmlpages','import-hospice')}}">
                <i class="bx bx-right-arrow-alt"></i>
                <span class="menu-item text-truncate" data-i18n="List">Hospice</span>
              </a>
            </li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','import-hospic-branches')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Hospice Branches</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','import-nurses')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Nurses</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','import-patients')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Patients</span>
            </a>
          </li>
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','import-patients')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List"> DeliverCareX Users </span>
            </a>
          </li>
        </ul>
      </li>
      <li class=" navigation-header text-truncate"><span data-i18n="User-Managment">Reports</span>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="notebook"></i><span class="menu-title text-truncate" data-i18n="DeliverCare User">Reports</span></a>
        <ul class="menu-content">
          <li>
            <a class="d-flex align-items-center" href="{{route('htmlpages','report-audit-trails')}}">
              <i class="bx bx-right-arrow-alt"></i>
              <span class="menu-item text-truncate" data-i18n="List">Audit Trails</span>
            </a>
          </li>
        </ul>
      </li>

      <!-- <li class=" navigation-header text-truncate"><span data-i18n="Users">Users</span>
      </li>

      <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="retweet"></i><span class="menu-title text-truncate" data-i18n="Content">Content</span></a>
        <ul class="menu-content">
          <li><a class="d-flex align-items-center" href="content-grid.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item text-truncate" data-i18n="Grid">Grid</span></a>
          </li>
          <li><a class="d-flex align-items-center" href="content-typography.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item text-truncate" data-i18n="Typography">Typography</span></a>
          </li>
          <li><a class="d-flex align-items-center" href="content-text-utilities.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item text-truncate" data-i18n="Text Utilities">Text Utilities</span></a>
          </li>
          <li><a class="d-flex align-items-center" href="content-syntax-highlighter.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item text-truncate" data-i18n="Syntax Highlighter">Syntax Highlighter</span></a>
          </li>
          <li><a class="d-flex align-items-center" href="content-helper-classes.html"><i class="bx bx-right-arrow-alt"></i><span class="menu-item text-truncate" data-i18n="Helper Classes">Helper Classes</span></a>
          </li>
        </ul>
      </li>
      <li class=" nav-item"><a href="colors.html"><i class="menu-livicon" data-icon="drop"></i><span class="menu-title text-truncate" data-i18n="Colors">Colors</span></a>
      </li> -->

    </ul>
  </div>
</div>
<!-- END: Main Menu-->