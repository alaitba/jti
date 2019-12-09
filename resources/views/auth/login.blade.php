<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>

    <title>::Login Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Latest updates and statistic charts">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            google: {"families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>
    <!--end::Web font -->

    <!--begin::Base Styles -->



    <link href="/core/adminLTE/assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/core/adminLTE/assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css"/>
    <!--end::Base Styles -->
    <link href="/core/css/core.css" rel="stylesheet" type="text/css"/>
    {{--<link rel="shortcut icon" href="/adminLTE/assets/demo/default/media/img/logo/favicon.ico" />--}}
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-37564768-1', 'auto');
        ga('send', 'pageview');

    </script>
</head>
<!-- end::Head -->


<!-- end::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">



<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2"
         id="m_login" style="background-image: url(/core/adminLTE/assets/app/media/img/bg/bg-3.jpg)">
        <div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
            <div class="m-login__container">
                <div class="m-login__signin">
                    <div class="m-login__head">
                        <h3 class="m-login__title">Авторизуйтесь для начала работы {{ env('APP_NAME') }}</h3>
                    </div>

                    <form class="m-login__form m-form ajax" action="{{route('admin.post.login')}}" method="post"
                          id="adminAuthForm" data-block-element=".login-box-body" data-block-type="page">
                        <div class="form-group m-form__group">
                            <input type="text" name="email" class="form-control m-input" placeholder="Ваш Email"
                                   id="email" autocomplete="off">
                            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                            <p class="help-block text-center"></p>
                        </div>
                        <div class="form-group m-form__group">
                            <input class="form-control m-input m-login__form-input--last" type="Password"
                                   placeholder="Ваш Пароль" id="password" name="password">
                            <p class="help-block text-center"></p>
                        </div>
                        <div class="row m-login__form-sub">
                            <div class="col m--align-left">
                                <label class="m-checkbox m-checkbox--focus">
                                    <input type="checkbox" name="remember"> Запомнить меня
                                    <span></span>
                                </label>
                            </div>
                            <div class="col m--align-right">
                                <a href="javascript:;" id="m_login_forget_password" class="m-link">Забыли пароль?</a>
                            </div>
                        </div>
                        <div class="m-login__form-action">
                            <input type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air" value="Войти">

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/core/adminLTE/assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
<script src="/core/adminLTE/assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
<!--end::Base Scripts -->
<!--begin::Page Snippets -->
<script src="/core/adminLTE/assets/snippets/custom/pages/user/login.js" type="text/javascript"></script>
<script src="/core/js/vendors/sweetalert2/dist/sweetalert2.all.min.js"></script>

<!--end::Page Snippets -->
<script src="/core/js/core.js"></script>


</body>
<!-- end::Body -->
</html>


