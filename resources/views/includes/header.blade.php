<!-- BEGIN: Header-->
<div class="header-navbar-shadow"></div>
<nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
  <div class="navbar-wrapper">
    <div class="navbar-container content">
      <div class="navbar-collapse" id="navbar-mobile">
        <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav">
            <li class="nav-item mobile-menu d-xl-none mr-auto">
              <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="javascript:void(0);">
                <i class="ficon bx bx-menu"></i>
              </a>
            </li>
          </ul>
          <!-- <ul class="nav navbar-nav bookmark-icons">
            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon bx bx-envelope"></i></a></li>
            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon bx bx-chat"></i></a></li>
            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon bx bx-check-circle"></i></a></li>
            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calendar.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon bx bx-calendar-alt"></i></a></li>
          </ul> 
          <ul class="nav navbar-nav">
            <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon bx bx-star warning"></i></a>
              <div class="bookmark-input search-input">
                <div class="bookmark-input-icon"><i class="bx bx-search primary"></i></div>
                <input class="form-control input" type="text" placeholder="Explore Frest..." tabindex="0" data-search="template-search">
                <ul class="search-list"></ul>
              </div>
            </li>
          </ul> -->
        </div>

        <ul class="nav navbar-nav float-right">
        @if(Auth::user()->user_type == 2 && checkCartActive() === true)
          <li class="nav-item d-block">
            <a class="nav-link nav-link-cart position-relative" href="{{ route('my-shopping-cart') }}">
              <i class='ficon bx bx-cart-alt'></i>
              <span class="badge badge-pill badge-danger badge-up badge-blank"></span>
            </a>
          </li>
          @endif


          <li class="dropdown dropdown-user nav-item">
            <a class="dropdown-toggle nav-link dropdown-user-link" href="javascript:void(0);" data-toggle="dropdown">
              <div class="user-nav d-sm-flex d-none">
                <span class="user-name">{{auth()->user()->first_name. ' '. auth()->user()->last_name}}</span>
                {{-- <span class="user-status text-muted">{{auth()->user()->roles->role_title}}</span> --}}
              </div>

              @if(auth()->user()->profile_picture)
              <span>
                <img class="round" src="{{asset('assets/upload/profile-pic/'.auth()->user()->profile_picture)}}" alt="avatar" height="40" width="40">
                @else
                 <img class="round" src="{{asset('assets/img/user-default.jpg')}}" alt="avatar" height="40" width="40">
              </span>
              @endif

            </a>
            <div class="dropdown-menu dropdown-menu-right pb-0">
              <a class="dropdown-item" href="{{route('admin.profile')}}">
                <i class="bx bx-cog mr-50"></i> Account Settings
              </a>
              <!-- <a class="dropdown-item" href="app-email.html">
                <i class="bx bx-envelope mr-50"></i> My Inbox
              </a>
              <a class="dropdown-item" href="app-todo.html">
                <i class="bx bx-check-square mr-50"></i> Task
              </a>
              <a class="dropdown-item" href="app-chat.html">
                <i class="bx bx-message mr-50"></i> Chats
              </a> -->
              <div class="dropdown-divider mb-0"></div>

              @if(isset($_GET['isFound']))
              <a class="dropdown-item" href="{{route('logoutPost')}}">
                <i class="bx bx-power-off mr-50"></i> Logout
              </a>                  
              @endif

              <a class="dropdown-item" href="{{route('logout')}}">
                <i class="bx bx-power-off mr-50"></i> Logout
              </a>

            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<!-- END: Header-->