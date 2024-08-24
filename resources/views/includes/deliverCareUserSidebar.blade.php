<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="">
                    <div class="brand-logo">
                        <img src="{{ asset('assets/img/small-logo.png') }}" alt="avatar" class="logo" width="26px"
                            height="26px" />
                    </div>
                    <h2 class="brand-text mb-0">DeliverCare</h2>
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

            <li class="active  nav-item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="menu-livicon" data-icon="desktop"></i>
                    <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard  </span>
                </a>
            </li>


                <li class=" navigation-header text-truncate"><span data-i18n="DeliverCare">DeliverCare</span>
                </li>


                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="gears"></i><span
                            class="menu-title text-truncate" data-i18n="DeliverCare User">Role</span></a>
                    <ul class="menu-content">
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('roles') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Role List">Role List</span>
                            </a>
                        </li>
                    </ul>
                </li>

              @if (whoCanCheck(config('app.arrWhoCanCheck'), 'user_add') === true ||
              whoCanCheck(config('app.arrWhoCanCheck'), 'user_listing') === true)
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span
                            class="menu-title text-truncate" data-i18n="DeliverCare User">DeliverCare User</span></a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'user_listing') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('user-list') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="User List">User List</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'user_add') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('show-user-form') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="User Add">User Add</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_listing') === true)
                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="building"></i><span
                                class="menu-title text-truncate" data-i18n="Pharmacy">Pharmacy</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_listing') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('pharmacy-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Pharmacy List">Pharmacy List</span>
                                    </a>
                                </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('store-pharmacy') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Pharmacy Add">Pharmacy Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'partner_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'partner_listing') === true)
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span
                            class="menu-title text-truncate" data-i18n="partners">Partners</span></a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'partner_listing') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('partners-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Partner List">Partner List</span>
                                </a>
                            </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'partner_add') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('partners-add') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Partner Add">Partner Add</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'facility_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'facility_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'branch_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'branch_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'assign_nurse') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'patients_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'patients_listing') === true)
                <li class=" navigation-header text-truncate"><span data-i18n="Hospice Managment">Hospice Managment</span>
                </li>
                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_listing') === true)
                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="building"></i><span
                                class="menu-title text-truncate" data-i18n="Hospice">Hospice</span></a>
                        <ul class="menu-content">
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('hospice-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Hospice List">Hospice List</span>
                                    </a>
                                </li>
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('show-hospice-form') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Hospice Add">Hospice Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'facility_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'facility_listing') === true)
                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span
                                class="menu-title text-truncate" data-i18n="Facility">Facility</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'facility_listing') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('facilities-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Facility List">Facility List</span>
                                    </a>
                                </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'facility_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('admin.facilities-add') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Facility Add">Facility Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'branch_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'branch_listing') === true)
                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="diagram"></i><span
                                class="menu-title text-truncate" data-i18n="Branch">Branch</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'branch_listing') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('branch-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Branch List">Branch List</span>
                                    </a>
                                </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'branch_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('admin.branch-add') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Branch Add">Branch Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                 @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_add') === true)
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span
                            class="menu-title text-truncate" data-i18n="Users">Users</span></a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_listing') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('hospice-user-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Users List">User List</span>
                                </a>
                            </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_add') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('hospice-show-user-form') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="User Add">User Add</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
                @endif

                 @if (whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_listing') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'assign_nurse') === true)
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span
                            class="menu-title text-truncate" data-i18n="Nurses">Nurses</span></a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_listing') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('nurse-user-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Nurse List">Nurse List</span>
                                </a>
                            </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_add') === true)
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('nurse-show-user-form') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Nurse Add">Nurse Add</span>
                                </a>
                            </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'assign_nurse') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('assign-nurse') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Assign Nurse">Assign Nurse</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'patients_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'patients_listing') === true)
                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span
                                class="menu-title text-truncate" data-i18n="Patients">Patients</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'patients_listing') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('patients-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Patient List">Patient List</span>
                                    </a>
                                </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'patients_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('show-patients-form') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Patient Add">Patient Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                 @if (whoCanCheck(config('app.arrWhoCanCheck'), 'refills_in_queue') === true)
                <li class="navigation-header text-truncate">
                    <span data-i18n="Refill Management">Refill Management</span>
                </li>
                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'refills_in_queue') === true)
                <li class="nav-item">
                    <a href="{{ route('refillsInQueue-list') }}">
                        <i class="menu-livicon" data-icon="priority-high"></i>
                        <span class="menu-title text-truncate" data-i18n="Refills In-Queue">Refills In-Queue</span>
                    </a>
                </li>
                @endif

                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'latest_orders') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'all_orders') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'place_orders')  === true 
                    ||  whoCanCheck(config('app.arrWhoCanCheck'), 'telephonic_orders')  === true
                     ||
                        whoCanCheck(config('app.arrWhoCanCheck'), 'refills') === true)
                <li class=" nav-item">
                    <a>
                        <i class="menu-livicon" data-icon="shoppingcart"></i>
                        <span class="menu-title text-truncate" data-i18n="Orders">Orders</span>
                    </a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'latest_orders') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('latest-orders-sa') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Latest Orders">Latest Orders</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'all_orders') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('all-orders-sa') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Refills Orders">Refills Orders</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'refills') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('refillsIn-list') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="All Orders Received">All Orders Received</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'place_orders') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('place-order') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Place Order">Place Order</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'telephonic_orders') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('index-offline-orders') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Telephonic Orders">Telephonic Orders</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_add') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_listing') === true)
                    <li class=" navigation-header text-truncate"><span data-i18n="Shipping Carriers">Shipping Carriers</span>
                    </li>

                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="truck"></i><span class="menu-title text-truncate" data-i18n="Shipping Carriers">Shipping Carriers</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_listing') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('shipping-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Shipping Carriers List">Shipping Carrier List</span>
                                    </a>
                                </li>
                            @endif
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_add') === true)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('show-shipping-form') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Shipping Carrier Add">Shipping Carrier Add</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif
                    @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-branches') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import-nurse') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import/patient') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import-hospice') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import-delivercare-user') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import-facility') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'import-branch') === true ||
                    whoCanCheck(config('app.arrWhoCanCheck'), 'newleaf-order-ids') === true)
                <li class=" navigation-header text-truncate"><span data-i18n="Import">Import</span>
                </li>

                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="file-import"></i><span
                            class="menu-title text-truncate" data-i18n="Import">Import</span></a>
                    <ul class="menu-content">
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-hospice') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('import-hospice') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Hospice">Hospice </span>
                            </a>
                        </li>
                        @endif
                        {{-- @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-branches') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('import-branches') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Hospice Branches">Hospice Branches</span>
                            </a>
                        </li>
                        @endif --}} 
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-nurse') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('import-nurses') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Nurses">Nurses</span>
                            </a>
                        </li>
                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import/patient') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('import-patients') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Patients">Patients</span>
                            </a>
                        </li>
                        @endif
                         @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-delivercare-user') === true)
                        <li>
                            <a class="d-flex align

                            -items-center" href="{{ route('import-delivercare') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="DeliverCareX Users"> DeliverCareX Users</span>
                            </a>
                        </li>

                        @endif
                             @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-facility') === true)
                        <li>
                            <a class="d-flex align

                            -items-center" href="{{ route('import-facility') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Facility">  Facility </span>
                            </a>
                        </li>

                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'import-branch') === true)
                        <li>
                            <a class="d-flex align

                            -items-center" href="{{ route('import-branchs') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="Branches">  Branches </span>
                            </a>
                        </li>

                        @endif
                        @if (whoCanCheck(config('app.arrWhoCanCheck'), 'newleaf-order-ids') === true)
                        <li>
                            <a class="d-flex align-items-center" href="{{ route('import-newleaf') }}">
                                <i class="bx bx-right-arrow-alt"></i>
                                <span class="menu-item text-truncate" data-i18n="NewLeaf Order IDs">  NewLeaf Order IDs </span>
                            </a>
                        </li>

                        @endif
                       
                    </ul>
                </li>
                @endif

            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'email_template_listing') === true)
                <li class=" navigation-header text-truncate"><span data-i18n="Global Settings">Global Settings</span>
                </li>

                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="gear"></i><span
                            class="menu-title text-truncate" data-i18n="Global Settings">Global Settings</span></a>
                    <ul class="menu-content">
                       
                            <li>
                                <a class="d-flex align-items-center" href="{{ route('email-template-list') }}">
                                    <i class="bx bx-right-arrow-alt"></i>
                                    <span class="menu-item text-truncate" data-i18n="Email Templates">Email Templates</span>
                                </a>
                            </li>
                    

                    </ul>
                </li>
                    @endif

                @if (whoCanCheck(config('app.arrWhoCanCheck'), 'activity_listing') === true)
                    <li class=" navigation-header text-truncate"><span data-i18n="Reports">Reports</span>
                    </li>


                    <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="notebook"></i><span
                                class="menu-title text-truncate" data-i18n="Reports">Reports</span></a>
                        <ul class="menu-content">
                            @if (whoCanCheck(config('app.arrWhoCanCheck'), 'activity_listing') === true || Auth::user()->user_type == 2)
                                <li>
                                    <a class="d-flex align-items-center" href="{{ route('activity-list') }}">
                                        <i class="bx bx-right-arrow-alt"></i>
                                        <span class="menu-item text-truncate" data-i18n="Audit Trails">Audit Trails</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

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
