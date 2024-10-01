<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="theme-color" content="#ffffff">
    <title>StudentAidBC - Dashboard</title>
    <meta content="StudentAid BC helps eligible students with the cost of their post-secondary education through loans, grants, scholarships, and other programs." name="description" />
    <meta content="Student Aid BC" name="author" />
    <meta name="google-site-verification" content="6YtGWZRd5oHLRR8AZqViNt2atcn_0VVAd1Pz3MYiVek">
    <!-- App favicon -->
    <link rel="shortcut icon" href="/dashboard/images/admin/favicon.ico">
    <link rel="stylesheet" href="/dashboard/css/app.css" type="text/css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    {{--    <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">--}}
    <link rel="stylesheet" href="/dashboard/css/custom_bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="/dashboard/css/iggy_bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="/dashboard/css/iggy_overrides.css" type="text/css" />

    <style type="text/css">
        aside{
            border-right: 1px solid #d3d9e0;
            border-left: 1px solid #d3d9e0;
            /*background: #f1f3f5;*/
        }
    </style>
</head>
<body>

<div id="app">
    <header>
        <dashboard-navbar :auth="false"></dashboard-navbar>
    </header>

    <section class="container-fluid pt-5 mt-2">
        <div class="row">
            <aside class="d-none d-xl-block col-xl-2 p-0 mb-5 pb-5">
                @yield('aside_left')
            </aside>
            <section class="col-sm-12 col-lg-9 col-xl-8 page-content mb-5 pb-5 bg-white">
                @yield('content')
            </section>
            <aside class="d-none d-md-block col-lg-3 col-xl-2 p-0 mb-5 pb-5">
                @yield('aside_right')
            </aside>
        </div>

    </section>
    @if(session()->has('DEBUG') && env('APP_DEBUG') == true)
        <div id="debug_box" style="position: fixed; bottom: 0; left: 0; width: 100%; overflow: scroll; height: 65px; background: #eee; margin-bottom: 30px;">
            <button id="debug_close_btn" class="btn btn-outline-danger m-3 btn-sm float-right" type="button">Close</button>
            <button id="debug_expand_btn" class="btn btn-outline-warning mt-3 btn-sm float-right" type="button">Expand</button>
            <button id="debug_minimize_btn" class="btn btn-outline-warning mt-3 btn-sm float-right" type="button" style="display: none;">Minimize</button>
            <a id="debug_clear_btn" class="btn btn-outline-info mr-3 mt-3 btn-sm float-right" href="/dashboard/clear-debugger">Clear</a>
            <?php
            echo "<pre class='p-3'>";
            echo "GUID: " . session(env('GUID_SESSION_VAR')) . "<br>";
            //            print_r(session('DEBUG'));
            foreach (session()->get('DEBUG') as $s) echo $s . "<br>";
            echo "</pre>";
            ?>
        </div>

    @endif

    <footer class="">
        @include('layouts.footer-none-auth')
    </footer>

</div>
<script src="/dashboard/js/es6-promise.auto.min.js"></script>
<script src="/dashboard/js/app.js?v=1.8"></script>


{{--<script src="//code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>--}}

<script src="/dashboard/js/jquery-3.6.0.min.js"></script>
{{--<script src="/dashboard/js/jquery-3.5.1.min.js"></script>--}}
<script src="/dashboard/js/popper.min.js"></script>
{{--<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>--}}
<script src="/dashboard/js/bootstrap.bundle.min.js"></script>
<script>
    // Google Tag Manager
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PW39L5P');
</script>
@if(session()->has('DEBUG') && env('APP_DEBUG') == true)
    <script>
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
    </script>
@endif
</body>
</html>
