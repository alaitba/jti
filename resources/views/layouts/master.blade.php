<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="editor-objects-url" content="{{ route('admin.wysiwyg.objects') }}">

    <title> @yield('title') :: Админ панель </title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
        WebFont.load({
            //google: {"families":["Play:300,400,500,600,700","Poppins:300,400,500,600,700","Comfortaa:300,400,500,600,700", "Open Sans:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            google: {"families": ["Play:300,400,500,600,700", "Roboto:300,400,500,600,700"]},
            active: function () {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <link href="/core/adminLTE/assets/vendors/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet"
          type="text/css"/>
    <link href="/core/adminLTE/assets/vendors/base/vendors.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/core/adminLTE/assets/demo/default/base/style.bundle.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="/core/adminLTE/assets/demo/default/media/img/logo/favicon.ico"/>
    <link href="/core/css/core.css" rel="stylesheet" type="text/css"/>
    <link href="/core/css/crop.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/core/css/jquery.tag-editor.css">
    <link href="/core/css/select2.min.css" rel="stylesheet" />
    @stack('css')
</head>


<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<div class="m-grid m-grid--hor m-grid--root m-page">
    <header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
        <div class="m-container m-container--fluid m-container--full-height">
            <div class="m-stack m-stack--ver m-stack--desktop">

                <!-- BEGIN: Brand -->
                <div class="m-stack__item m-brand  m-brand--skin-dark ">
                    <div class="m-stack m-stack--ver m-stack--general">


                        <div class="m-stack__item m-stack__item--middle m-brand__tools">
                            <a href="javascript:;" id="m_aside_left_minimize_toggle"
                               class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block  ">
                                <span></span>
                            </a>
                            <a href="javascript:;" id="m_aside_left_offcanvas_toggle"
                               class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
                                <span></span>
                            </a>

                            <a id="m_aside_header_topbar_mobile_toggle" href="javascript:;"
                               class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
                                <i class="flaticon-more"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- END: Brand -->
                @include(config('project.views.defaults.admin_header_nav'))
            </div>
        </div>
    </header>

    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">

        <!-- BEGIN: Left Aside -->
        <button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
            <i class="la la-close"></i>
        </button>
        <div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">

            <!-- BEGIN: Aside Menu -->
            @include(config('project.views.defaults.admin_navigation'))
        </div>

        <div class="m-grid__item m-grid__item--fluid m-wrapper">
            <div class="m-content" style="padding: 5px 5px;">

                @yield('content')

            </div>
        </div>
    </div>

    <footer class="m-grid__item		m-footer ">
        <div class="m-container m-container--fluid m-container--full-height m-page__container">
            <div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
                <div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
							<span class="m-footer__copyright">
								2019 &copy;
								<a href="{{ route('admin') }}" class="m-link">Starter Kit</a>
							</span>
                </div>
                <div class="m-stack__item m-stack__item--right m-stack__item--middle m-stack__item--first">
                    <ul class="m-footer__nav m-nav m-nav--inline m--pull-right">
                        <li class="m-nav__item">
                            <a href="https://ibecsystems.com" target="_blank" class="m-nav__link">
                                <span class="m-nav__link-text">iBEC Systems</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>

<div class="m-scroll-top m-scroll-top--skin-top" data-toggle="m-scroll-top" data-scroll-offset="500"
     data-scroll-speed="300">
    <i class="la la-arrow-up"></i>
</div>


@include('layouts.modals')

<script src="/core/adminLTE/assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
<script src="/core/adminLTE/assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>

<script src="/core/adminLTE/assets/app/js/dashboard.js" type="text/javascript"></script>
<script src="/core/adminLTE/assets/demo/default/custom/components/base/toastr.js" type="text/javascript"></script>
<script src="/core/js/vendors/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="/core/js/vendors/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/core/js/vendors/cropperjs/dist/cropper.min.js" type="text/javascript"></script>
<script src="/core/js/vendors/jquery-cropper/dist/jquery-cropper.min.js" type="text/javascript"></script>
<script src="/core/js/vendors/ace/src/ace.js"></script>
<script src="/core/js/vendors/ace/src/mode-html.js"></script>
<script src="/core/js/vendors/ace/src/snippets/html.js"></script>
<script src="/core/js/crop.js"></script>
<script src="/core/js/core.js"></script>
<script src="/core/js/media.js"></script>
<script src="/core/js/jquery.caret.min.js"></script>
<script src="/core/js/jquery.tag-editor.min.js"></script>
<script src="/core/js/select2.min.js"></script>
@stack('modules')
</body>
</html>
