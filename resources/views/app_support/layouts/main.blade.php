<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="theme-color" content="#ffffff">
        <title>StudentAidBC - App Support</title>
        <meta content="StudentAid BC helps eligible students with the cost of their post-secondary education through loans, grants, scholarships, and other programs." name="description" />
        <meta content="Student Aid BC" name="author" />
        <meta name="google-site-verification" content="6YtGWZRd5oHLRR8AZqViNt2atcn_0VVAd1Pz3MYiVek">
        <!-- App favicon -->
        <link rel="shortcut icon" href="/dashboard/images/admin/favicon.ico">

        <!-- App css -->
        <link href="/dashboard/css/admin/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/dashboard/css/admin/icons.css" rel="stylesheet" type="text/css" />
        <link href="/dashboard/css/admin/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="/dashboard/css/admin/style.css" rel="stylesheet" type="text/css" />

        <!-- jQuery  -->
{{--        <script src="/dashboard/js/admin/jquery.min.js"></script>--}}
        <script src="/dashboard/js/jquery-3.6.0.min.js"></script>

        <script src="/dashboard/js/admin/bootstrap.bundle.min.js"></script>
        <script src="/dashboard/js/admin/metisMenu.min.js"></script>
        <script src="/dashboard/js/admin/jquery.slimscroll.min.js"></script>


    </head>

    <body>
        <div id="app_support_app">
            <header>
                @include('app_support.layouts.top-nav')
            </header>

            <section class="page-wrapper">
                <aside class="">
                    @include('app_support.layouts.left-nav')
                </aside>
                <section class="page-content">
                    @yield('content')
                </section>
            </section>

            <footer class="">
                @include('app_support.layouts.footer')
            </footer>

        </div>


        <script src="/dashboard/js/admin/app-support-app.js?v=2.2"></script>
    <script>

        (function ($) {

            'use strict';

            function initSlimscroll() {
                $('.slimscroll').slimscroll({
                    height: 'auto',
                    position: 'right',
                    size: "7px",
                    color: '#e0e5f1',
                    opacity: 1,
                    wheelStep: 5,
                    touchScrollStep: 50
                });
            }


            function initMetisMenu() {
                //metis menu
                $(".metismenu").metisMenu();
            }

            function initLeftMenuCollapse() {
                // Left menu collapse
                $('.button-menu-mobile').on('click', function (event) {
                    event.preventDefault();
                    $("body").toggleClass("enlarge-menu");
                    initSlimscroll();
                });
            }

            function initEnlarge() {
                if ($(window).width() < 1025) {
                    $('body').addClass('enlarge-menu');
                } else {
                    if ($('body').data('keep-enlarged') != true)
                        $('body').removeClass('enlarge-menu');
                }
            }





            function initActiveMenu() {
                // === following js will activate the menu in left side bar based on url ====
                $(".left-sidenav a").each(function () {
                    var pageUrl = window.location.href.split(/[?#]/)[0];
                    if (this.href == pageUrl) {
                        $(this).addClass("active");
                        $(this).parent().addClass("active"); // add active to li of the current link
                        $(this).parent().parent().addClass("in");
                        $(this).parent().parent().addClass("mm-show");
                        $(this).parent().parent().parent().addClass("mm-active");
                        $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
                        $(this).parent().parent().parent().addClass("active");
                        $(this).parent().parent().parent().parent().addClass("mm-show"); // add active to li of the current link
                        $(this).parent().parent().parent().parent().parent().addClass("mm-active");

                    }
                });
            }



            function init() {
                initSlimscroll();
                initMetisMenu();
                initLeftMenuCollapse();
                initEnlarge();
                initActiveMenu();
                //Waves.init();
            }

            init();

        })(jQuery);

        jQuery(document).ready(function(){
            $("#debug_close_btn").click(function(){
                $("#debug_box").css('height', '0px');
            });
            $("#debug_expand_btn").click(function(){
                $("#debug_box").css('height', '300px');
                $("#debug_expand_btn").hide();
                $("#debug_minimize_btn").show();
            });
            $("#debug_minimize_btn").click(function(){
                $("#debug_box").css('height', '65px');
                $("#debug_expand_btn").show();
                $("#debug_minimize_btn").hide();
            });
        });
    </script>
    </body>
</html>
