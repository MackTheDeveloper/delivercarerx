<html>

<head>
    @include('includes.head')
</head>

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-sticky footer-static" data-open="click"
    data-menu="vertical-menu-modern" data-col="2-columns">
    @include('includes.header')
     @if(Auth::user()->user_type == 1 || Auth::user()->user_type == null)
            @include('includes.deliverCareUserSidebar')
        @endif

        @if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
            @include('includes.hospiceAdminSidebar')
        @endif

        @if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
            @include('includes.branchAdminSidebar')
        @endif

         @if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
            @include('includes.nursesidebar')
        @endif
    @yield('content')
    @include('includes.footer')
    @include('includes.bottom')
</body>
<script>
    var isRtl = $('html').attr('data-textdirection') === 'rtl';
    @if (Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch (type) {
            case 'info':
                /* toastr.info("{{ Session::get('message') }}"); */
                toastr['info']('{{ Session::get('message') }}', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
                break;

            case 'warning':
                /* toastr.warning("{{ Session::get('message') }}"); */
                toastr['warning']('{{ Session::get('message') }}', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
                break;

            case 'success':
                /* toastr.success("{{ Session::get('message') }}"); */
                toastr['success']('{{ Session::get('message') }}', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
                break;

            case 'error':
                /* toastr.error("{{ Session::get('message') }}", {
                    positionClass: 'toast-bottom-right',
                    rtl: isRtl
                }); */
                toastr['error']('{{ Session::get('message') }}', '', {
                    positionClass: 'toast-bottom-right',
                    closeButton: true,
                    tapToDismiss: false,
                    rtl: isRtl,
                });
                break;
        }
    @endif
</script>

</html>
