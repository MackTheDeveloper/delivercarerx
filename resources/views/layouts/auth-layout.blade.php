<html data-textdirection="ltr">

<head>
    @include('includes.head')
</head>

<?php
/* SSO PARM CHECK START */
if(\Route::current()->getName()== 'login'){ 
    //echo "test";

/*
    public class Startup
    {
        public void Configure(IApplicationBuilder app)
        {
            JwtSecurityTokenHandler.DefaultInboundClaimTypeMap.Clear();

            app.UseStaticFiles();

            app.UseCookieAuthentication(new CookieAuthenticationOptions
            {
                AuthenticationScheme = "cookies",
                AutomaticAuthenticate = true,
            });

            var oidcOptions = new OpenIdConnectOptions
            {
                AuthenticationScheme = "oidc",
                SignInScheme = "cookies",

                Authority = "https://demo.identityserver.io",
                ClientId = "mvc",
                ClientSecret = "secret",
                ResponseType = "code id_token",
                SaveTokens = true,
                GetClaimsFromUserInfoEndpoint = true,

                TokenValidationParameters = new TokenValidationParameters
                {
                    NameClaimType = "name",
                    RoleClaimType = "role"
                }
            };

            oidcOptions.Scope.Clear();
            oidcOptions.Scope.Add("openid");
            oidcOptions.Scope.Add("profile");
            oidcOptions.Scope.Add("api1");

            app.UseOpenIdConnectAuthentication(oidcOptions);

            app.UseMvcWithDefaultRoutes();
        }
    }*/
}
 /*SSO PARM END START*/
?>

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
