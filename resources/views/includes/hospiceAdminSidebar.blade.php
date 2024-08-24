<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="">
                    <div class="brand-logo">
                        <img src="{{ asset('assets/img/small-logo.png') }}" alt="avatar" class="logo" width="26px"
                            height="26px" />
                    </div>
                    <h2 class="brand-text mb-0">Hospice Users</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i
                        class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i
                        class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary"
                        data-ticon="bx-disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
            data-icon-style="lines">
            <li class="nav-item">
                <a href="{{ route('nursePatients-list') }}">
                    <i class="menu-livicon" data-icon="users"></i>
                    <span class="menu-title text-truncate" data-i18n="Patients">Patients</span>
                </a>
            </li>

            <li class=" navigation-header text-truncate"><span data-i18n="Hospice User">Hospice Users</span>
            </li>

                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span
                            class="menu-title text-truncate" data-i18n="Facility">Facility</span></a>
                    <ul class="menu-content">
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('facilities-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Facility List">Facility List</span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('admin.facilities-add') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Facility Add">Facility Add</span>
                                </a>
                            </li>
                    </ul>
                </li>


                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span
                            class="menu-title text-truncate" data-i18n="Branch">Branch</span></a>
                    <ul class="menu-content">
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('branch-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Branch List">Branch List</span>
                                </a>
                            </li>
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('admin.branch-add') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Branch Add">Branch Add</span>
                                </a>
                            </li>
                    </ul>
                </li>

        <li class="nav-item">
            <a href="{{route('refillsInQueue-list')}}">
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
                        <a class="d-flex align-items-center" href="{{ route('latest-orders-sa') }}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item text-truncate" data-i18n="Latest Orders">Latest Orders</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{route('all-orders-sa')}}">
                            <i class="bx bx-right-arrow-alt"></i>
                            <span class="menu-item text-truncate" data-i18n="Refills Orders">Refills Orders</span>
                        </a>
                    </li>
                    <li>
                        <a class="d-flex align-items-center" href="{{route('refillsIn-list')}}">
                            <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-title text-truncate" data-i18n="All Orders Received">All Orders Received</span>
                        </a>
                    </li>
                </ul>
            </li>

                <li class=" navigation-header text-truncate"><span data-i18n="Reports">Reports</span>
                </li>


                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="notebook"></i><span
                            class="menu-title text-truncate" data-i18n="Reports">Reports</span></a>
                    <ul class="menu-content">
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('activity-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Audit Trails">Audit Trails</span>
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
