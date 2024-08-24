<html data-textdirection="ltr">

<head>
    @include('includes.head')
</head>

<body class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page"
    data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
    @yield('content')
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
