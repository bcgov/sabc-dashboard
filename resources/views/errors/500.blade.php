<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="theme-color" content="#ffffff">
    <title>StudentAidBC - 500 Error</title>
    <meta content="StudentAid BC helps eligible students with the cost of their post-secondary education through loans, grants, scholarships, and other programs." name="description" />
    <meta content="Student Aid BC" name="author" />
    <meta name="google-site-verification" content="6YtGWZRd5oHLRR8AZqViNt2atcn_0VVAd1Pz3MYiVek">
    <!-- App favicon -->
    <link rel="shortcut icon" href="/dashboard/images/admin/favicon.ico">
    <link rel="stylesheet" href="/dashboard/css/app.css" type="text/css" />
    <link rel="stylesheet" href="/dashboard/css/bootstrap.min.css">
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
        <h1>&nbsp;</h1>
    </header>

    <section class="container-fluid pr-3 pl-3">
        <div class="row">
            <aside class="d-none d-xl-block col-xl-2 p-0 mb-5 pb-5"></aside>
            <section class="col-sm-12 col-lg-9 col-xl-8 page-content mb-5 pb-5 bg-white">
                <dashboard-500></dashboard-500>
            </section>
            <aside class="d-none d-md-block col-lg-3 col-xl-2 p-0 mb-5 pb-5"></aside>
        </div>
    </section>

    <footer class="">
        <h1>&nbsp;</h1>
    </footer>

</div>
<script src="/dashboard/js/es6-promise.auto.min.js"></script>
<script src="/dashboard/js/app.js?v=1.0"></script>
<script src="/dashboard/js/formatter.min.js?v=1.0"></script>


{{--<script src="//code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>--}}

<script src="/dashboard/js/jquery-3.6.0.min.js"></script>
{{--<script src="/dashboard/js/jquery-3.5.1.min.js"></script>--}}
<script src="/dashboard/js/popper.min.js"></script>
{{--<script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>--}}
<script src="/dashboard/js/bootstrap.bundle.min.js"></script>
<script src="/dashboard/js/iCheck.js?v=1.0"></script>

</body>
</html>
