<!DOCTYPE html>
<!--[if IEMobile 7 ]><html class="no-js iem7 oldie"><![endif]-->
<!--[if lt IE 7 ]><html class="no-js ie6 oldie" lang="en"><![endif]-->
<!--[if IE 7 ]><html class="no-js ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8 ]><html class="no-js ie8 oldie" lang="en"><![endif]-->
<!--[if (gte IE 9)|(gt IEMobile 7)|!(IEMobile)|!(IE)]><!-->
<html class="js flexbox flexboxlegacy canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers no-applicationcache svg inlinesvg smil svgclippaths" lang="en" style manifest>
<!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,">
        <title>Student Loan Application</title>
        <meta content="StudentAid BC helps eligible students with the cost of their post-secondary education through loans, grants, scholarships, and other programs." name="description" />
        <meta content="Student Aid BC" name="author" />
        <meta name="google-site-verification" content="6YtGWZRd5oHLRR8AZqViNt2atcn_0VVAd1Pz3MYiVek">
        <!-- App favicon -->
        <link rel="shortcut icon" href="/dashboard/images/admin/favicon.ico">
        <link rel="stylesheet" href="/dashboard/css/app.css" type="text/css" />
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
{{--        <link rel="stylesheet" href="/dashboard/css/custom_bootstrap.css" type="text/css" />--}}
{{--        <link rel="stylesheet" href="/dashboard/css/iggy_bootstrap.css" type="text/css" />--}}

        <link rel="stylesheet" type="text/css" href="/dashboard/css/lc-application.css">

        <style id="mfstyle" type="text/css">.___ {fill:rgb(0,0,0);font:16px  Arial ;}.__A {vertical-align: middle;}.__B { height:100%;color:rgb(0,0,0);font:16px 'Arial';text-align:left;vertical-align:middle;width:100%;}.__C {height:100%;width:100%;}.__D {left:0;top:0px;}.__E {-moz-border-bottom-left-radius:14px;-moz-border-bottom-right-radius:14px;-moz-border-top-left-radius:14px;-moz-border-top-right-radius:14px;-webkit-border-bottom-left-radius:14px;-webkit-border-bottom-right-radius:14px;-webkit-border-top-left-radius:14px;-webkit-border-top-right-radius:14px;background-color: rgb(255,255,255);border-bottom-left-radius:14px;border-bottom-right-radius:14px;border-top-left-radius:14px;border-top-right-radius:14px;border:1px solid rgb(35,65,117);}.__F {fill:rgb(0,0,0);font:bold 12px Arial ;}.__G { height:100%;color:rgb(0,0,0);font:bold 32px'Arial';text-align:center;vertical-align:middle;width:100%;}.__H {-moz-border-bottom-left-radius:14px;-moz-border-bottom-right-radius:14px;-moz-border-top-left-radius:14px;-moz-border-top-right-radius:14px;-webkit-border-bottom-left-radius:14px;-webkit-border-bottom-right-radius:14px;-webkit-border-top-left-radius:14px;-webkit-border-top-right-radius:14px;background-color: rgb(255,255,255);border-bottom-left-radius:14px;border-bottom-right-radius:14px;border-top-left-radius:14px;border-top-right-radius:14px;border:1px solid rgb(255,255,255);}.__I { height:100%;color:rgb(0,0,0);font:32px 'Arial';text-align:center;vertical-align:middle;width:100%;}.__J { height:100%;color:rgb(189,54,47);font:20px 'Arial';text-align:center;vertical-align:middle;width:100%;}.__K {fill:rgb(255,255,255);font:bold 44px Arial ;}.__L { height:100%;color:rgb(255,255,255);font:bold 24px'Arial';text-align:center;vertical-align:middle;width:100%;}.__M { height:100%;color:rgb(255,255,255);font:bold 22px'Arial';text-align:center;vertical-align:middle;width:100%;}.__N {-moz-border-bottom-left-radius:6px;-moz-border-bottom-right-radius:6px;-moz-border-top-left-radius:6px;-moz-border-top-right-radius:6px;-webkit-border-bottom-left-radius:6px;-webkit-border-bottom-right-radius:6px;-webkit-border-top-left-radius:6px;-webkit-border-top-right-radius:6px;background-image: -moz-linear-gradient(top, rgb(240,91,91), rgb(193,50,50));background-image: -ms-linear-gradient(top, rgb(240,91,91), rgb(193,50,50));background-image: -o-linear-gradient(top, rgb(240,91,91), rgb(193,50,50));background-image: -webkit-linear-gradient(top, rgb(240,91,91), rgb(193,50,50));border-bottom-left-radius:6px;border-bottom-right-radius:6px;border-top-left-radius:6px;border-top-right-radius:6px;border:1px solid rgb(199,69,68);}.__O {fill:rgb(255,255,255);font:bold 22px Arial ;}.__P { height:100%;color:rgb(174,110,0);font:bold 22px'Arial';text-align:center;vertical-align:middle;width:100%;}.__Q {-moz-border-bottom-left-radius:6px;-moz-border-bottom-right-radius:6px;-moz-border-top-left-radius:6px;-moz-border-top-right-radius:6px;-webkit-border-bottom-left-radius:6px;-webkit-border-bottom-right-radius:6px;-webkit-border-top-left-radius:6px;-webkit-border-top-right-radius:6px;background-image: -moz-linear-gradient(top, rgb(254,217,111), rgb(254,188,74));background-image: -ms-linear-gradient(top, rgb(254,217,111), rgb(254,188,74));background-image: -o-linear-gradient(top, rgb(254,217,111), rgb(254,188,74));background-image: -webkit-linear-gradient(top, rgb(254,217,111), rgb(254,188,74));border-bottom-left-radius:6px;border-bottom-right-radius:6px;border-top-left-radius:6px;border-top-right-radius:6px;border:1px solid rgb(229,187,90);}.__R {fill:rgb(174,110,0);font:bold 22px Arial ;}.__S { height:100%;color:rgb(101,125,30);font:bold 22px'Arial';text-align:center;vertical-align:middle;width:100%;}.__T {-moz-border-bottom-left-radius:6px;-moz-border-bottom-right-radius:6px;-moz-border-top-left-radius:6px;-moz-border-top-right-radius:6px;-webkit-border-bottom-left-radius:6px;-webkit-border-bottom-right-radius:6px;-webkit-border-top-left-radius:6px;-webkit-border-top-right-radius:6px;background-image: -moz-linear-gradient(top, rgb(200,225,130), rgb(159,202,85));background-image: -ms-linear-gradient(top, rgb(200,225,130), rgb(159,202,85));background-image: -o-linear-gradient(top, rgb(200,225,130), rgb(159,202,85));background-image: -webkit-linear-gradient(top, rgb(200,225,130), rgb(159,202,85));border-bottom-left-radius:6px;border-bottom-right-radius:6px;border-top-left-radius:6px;border-top-right-radius:6px;border:1px solid rgb(165,195,100);}.__U {fill:rgb(101,125,30);font:bold 22px Arial ;}.__V {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;background-color: rgb(204,229,246);border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(173,225,241);}.__W { height:100%;color:rgb(33,125,187);font:12px 'Arial';text-align:left;vertical-align:middle;width:100%;}.__X {fill:rgb(33,125,187);font:12px  Arial ;}.__Y {-moz-border-bottom-left-radius:7px;-moz-border-bottom-right-radius:7px;-moz-border-top-left-radius:7px;-moz-border-top-right-radius:7px;-webkit-border-bottom-left-radius:7px;-webkit-border-bottom-right-radius:7px;-webkit-border-top-left-radius:7px;-webkit-border-top-right-radius:7px;background-color: rgb(236,240,241);border-bottom-left-radius:7px;border-bottom-right-radius:7px;border-top-left-radius:7px;border-top-right-radius:7px;border:1px solid rgb(202,208,210);}.__Z {fill:rgb(68,68,68);font:bold 28px Arial ;}.__a {fill:rgb(33,125,187);font:20px  Arial ;}.__b { height:100%;color:rgb(52,73,94);font:20px 'Arial';text-align:left;vertical-align:middle;width:100%;}.__c {fill:rgb(52,73,94);font:bold 20px Arial ;}.__d {fill:rgb(52,73,94);font:20px  Arial ;}.__e { height:100%;color:rgb(255,255,255);font:bold 14px'Arial';text-align:center;vertical-align:middle;width:100%;}.__f {-moz-border-bottom-left-radius:8px;-moz-border-bottom-right-radius:8px;-moz-border-top-left-radius:8px;-moz-border-top-right-radius:8px;-webkit-border-bottom-left-radius:8px;-webkit-border-bottom-right-radius:8px;-webkit-border-top-left-radius:8px;-webkit-border-top-right-radius:8px;background-color: rgb(0,0,255);border-bottom-left-radius:8px;border-bottom-right-radius:8px;border-top-left-radius:8px;border-top-right-radius:8px;border:1px solid rgb(0,0,255);}.__g {fill:rgb(255,255,255);font:bold 14px Arial ;}.__h {border-bottom:3px solid rgb(252,185,41);}.__i {fill:rgb(68,68,68);font:28px  Arial ;}.__j { height:100%;color:rgb(192,192,192);font:16px 'Arial';text-align:right;vertical-align:middle;width:100%;}.__k { height:100%;color:rgb(192,192,192);font:16px 'Arial';text-align:left;vertical-align:middle;width:100%;}.__l { height:100%;color:rgb(0,0,0);font:20px 'Arial';text-align:center;vertical-align:middle;width:100%;}.__m {background-color: rgb(212,208,200);border:1px outset rgb(0,0,0);}.__n {fill:rgb(0,0,0);font:20px  Arial ;}.__o {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;background-color: rgb(255,244,227);border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(254,231,212);}.__p {fill:rgb(223,141,34);font:bold 20px Arial ;}.__q {fill:rgb(223,141,34);font:20px  Arial ;}.__r { height:100%;-webkit-border-radius: 14;width:100%;}.__s {fill:rgb(255,0,0);font:20px  Arial ;}.__t {fill:rgb(192,192,192);font:16px  Arial ;}.__u { height:100%;width:100%;}.__v { height:100%;color:rgb(52,73,95);font:20px 'Arial';text-align:left;vertical-align:middle;width:100%;}.__w {-moz-border-bottom-left-radius:11px;-moz-border-bottom-right-radius:11px;-moz-border-top-left-radius:11px;-moz-border-top-right-radius:11px;-webkit-border-bottom-left-radius:11px;-webkit-border-bottom-right-radius:11px;-webkit-border-top-left-radius:11px;-webkit-border-top-right-radius:11px;background-color: rgb(255,244,227);border-bottom-left-radius:11px;border-bottom-right-radius:11px;border-top-left-radius:11px;border-top-right-radius:11px;border:1px solid rgb(254,231,212);}.__x {fill:rgb(223,141,34);font:italic 20px Arial ;}.__y {fill:rgb(52,73,95);font:20px  Arial ;}.__z { height:100%;color:rgb(0,0,0);font:italic 20px'Arial';text-align:left;vertical-align:middle;width:100%;}.____ {-moz-border-bottom-left-radius:6px;-moz-border-bottom-right-radius:6px;-moz-border-top-left-radius:6px;-moz-border-top-right-radius:6px;-webkit-border-bottom-left-radius:6px;-webkit-border-bottom-right-radius:6px;-webkit-border-top-left-radius:6px;-webkit-border-top-right-radius:6px;background-color: rgb(52,73,94);border-bottom-left-radius:6px;border-bottom-right-radius:6px;border-top-left-radius:6px;border-top-right-radius:6px;border:1px solid rgb(52,73,94);}.___A {fill:rgb(255,255,255);font:20px  Arial ;}.___B { height:100%;color:rgb(52,73,94);font:20px 'Arial';text-align:right;vertical-align:middle;width:100%;}.___C { height:100%;color:rgb(0,0,0);font:16px 'Arial';text-align:center;vertical-align:middle;width:100%;}.___D {fill:rgb(52,73,94);font:18px  Arial ;}.___E {background-color: rgb(63,65,74);border:1px solid rgb(0,0,0);}.___F {fill:rgb(255,255,255);font:bold 20px Arial ;}.___G {border:1px solid rgb(0,0,0);}.___H { height:100%;-webkit-border-radius: 10;width:100%;}.___I { height:100%;color:rgb(33,125,187);font:bold 16px'Arial';text-align:center;vertical-align:middle;width:100%;}.___J {fill:rgb(33,125,187);font:bold 16px Arial ;}.___K { height:100%;color:rgb(52,73,94);font:20px 'Arial';text-align:left;vertical-align:top;width:100%;}.___L {fill:rgb(255,0,0);font:bold 20px Arial ;}.___M {fill:rgb(33,125,187);font:italic 20px Arial ;}.___N {fill:rgb(255,255,255);font:12px  Arial ;}.___O { height:100%;color:rgb(255,255,255);font:12px 'Arial';text-align:left;vertical-align:bottom;width:100%;}.___P {-moz-border-bottom-left-radius:11px;-moz-border-bottom-right-radius:11px;-moz-border-top-left-radius:11px;-moz-border-top-right-radius:11px;-webkit-border-bottom-left-radius:11px;-webkit-border-bottom-right-radius:11px;-webkit-border-top-left-radius:11px;-webkit-border-top-right-radius:11px;border-bottom-left-radius:11px;border-bottom-right-radius:11px;border-top-left-radius:11px;border-top-right-radius:11px;border:1px solid rgb(33,125,187);}.___Q {fill:rgb(68,68,68);font:italic bold 20px Arial ;}.___R {fill:rgb(255,0,0);font:bold 12px Arial ;}.___S { height:100%;color:rgb(255,255,255);font:bold 20px'Arial';text-align:center;vertical-align:middle;width:100%;}.___T {-moz-border-bottom-left-radius:6px;-moz-border-bottom-right-radius:6px;-moz-border-top-left-radius:6px;-moz-border-top-right-radius:6px;-webkit-border-bottom-left-radius:6px;-webkit-border-bottom-right-radius:6px;-webkit-border-top-left-radius:6px;-webkit-border-top-right-radius:6px;background-image: -moz-linear-gradient(top, rgb(52,151,218), rgb(41,128,186));background-image: -ms-linear-gradient(top, rgb(52,151,218), rgb(41,128,186));background-image: -o-linear-gradient(top, rgb(52,151,218), rgb(41,128,186));background-image: -webkit-linear-gradient(top, rgb(52,151,218), rgb(41,128,186));border-bottom-left-radius:6px;border-bottom-right-radius:6px;border-top-left-radius:6px;border-top-right-radius:6px;border:1px solid rgb(36,107,154);}.___U {background-color: rgb(255,255,255);}.___V {fill:rgb(33,125,187);font:bold 12px Arial ;}.___W {-moz-border-bottom-left-radius:14px;-moz-border-bottom-right-radius:14px;-moz-border-top-left-radius:14px;-moz-border-top-right-radius:14px;-webkit-border-bottom-left-radius:14px;-webkit-border-bottom-right-radius:14px;-webkit-border-top-left-radius:14px;-webkit-border-top-right-radius:14px;background-color: rgb(255,255,255);border-bottom-left-radius:14px;border-bottom-right-radius:14px;border-top-left-radius:14px;border-top-right-radius:14px;border:1px solid rgb(0,0,0);}.___X { height:100%;color:rgb(33,125,187);font:20px 'Arial';text-align:left;vertical-align:middle;width:100%;}.___Y {fill:rgb(52,73,94);font:12px  Arial ;}.___Z {-moz-border-bottom-left-radius:11px;-moz-border-bottom-right-radius:11px;-moz-border-top-left-radius:11px;-moz-border-top-right-radius:11px;-webkit-border-bottom-left-radius:11px;-webkit-border-bottom-right-radius:11px;-webkit-border-top-left-radius:11px;-webkit-border-top-right-radius:11px;background-color: rgb(204,229,246);border-bottom-left-radius:11px;border-bottom-right-radius:11px;border-top-left-radius:11px;border-top-right-radius:11px;border:1px solid rgb(0,0,0);}.___a { height:100%;color:rgb(0,0,0);font:20px 'Arial';text-align:left;vertical-align:middle;width:100%;}.___b {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;background-color: rgb(246,221,219);border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(241,199,200);}.___c {fill:rgb(189,54,47);font:bold 20px Arial ;}.___d {fill:rgb(189,54,47);font:20px  Arial ;}.___e { height:100%;color:rgb(189,54,47);font:20px 'Arial';text-align:left;vertical-align:top;width:100%;}.___f {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(0,0,0);}.___g {fill:rgb(0,0,255);font:16px  Arial ;text-decoration:underline;}.___h {fill:rgb(192,192,192);font:20px  Arial ;}.___i {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;background-color: rgb(255,244,227);border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(255,244,227);}.___j {-moz-border-bottom-left-radius:9px;-moz-border-bottom-right-radius:9px;-moz-border-top-left-radius:9px;-moz-border-top-right-radius:9px;-webkit-border-bottom-left-radius:9px;-webkit-border-bottom-right-radius:9px;-webkit-border-top-left-radius:9px;-webkit-border-top-right-radius:9px;background-color: rgb(204,229,246);border-bottom-left-radius:9px;border-bottom-right-radius:9px;border-top-left-radius:9px;border-top-right-radius:9px;border:1px solid rgb(254,231,212);}.___k {fill:rgb(33,125,187);font:bold 20px Arial ;}.___l { height:100%;color:rgb(223,141,34);font:20px 'Arial';text-align:left;vertical-align:middle;width:100%;}.___m {fill:rgb(0,0,0);font:italic bold 20px Arial ;}</style>
        <style type="text/css">
            nav.lc-navbar{
                background: #234175;
                height: 55px;
                margin: 0;
                padding: 0 0px;
                width: 100%;
                border-bottom: 3px solid #fcb929;
            }
            nav.lc-navbar a.navbar-brand{
                background: url(/dashboard/img/logo-dt-icon.png) no-repeat scroll 0 0;
                height: 45px;
                width: 120px;
                display: block;
                white-space: normal;
                line-height: 1;
                margin: 10.66666667px 0 0 3px;
                outline: none;
                border: 0;
                color: rgba(255, 255, 255, 0.9);
                padding: 4px 0 0 55px;
                font-size: 11.9px;
            }
            nav.lc-navbar button.navbar-toggler{
                color: rgba(255, 255, 255, 0.75);
                border-color: rgba(255, 255, 255, 0);
            }
            nav.lc-navbar button.navbar-toggler span{
                background-image: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e);
            }
            nav.lc-navbar .dropdown-toggle::after {
                display: none !important;
            }
        </style>
    </head>
    <body id="formBody" class="html not-front logged-in one-sidebar sidebar-second page-dashboard page-dashboard-apply page-dashboard-apply-application page-dashboard-apply-application-full-time page-dashboard-apply-application-full-time- page-dashboard-apply-application-full-time-54427042 page-dashboard-apply-application-full-time- page-dashboard-apply-application-full-time-20202021 section-dashboard m l">
{{--        <header id="app">--}}
{{--            @include('layouts.nav')--}}
{{--        </header>--}}


        <header>
            <nav class="lc-navbar navbar navbar-expand-lg navbar-dark fixed-top">
                <a href="/dashboard" class="navbar-brand">Back to Dashboard</a>&nbsp;
                <button type="button" class="navbar-toggler">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="navbarNavDropdown" class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto pt-1">
                        <li class="nav-item pl-3">
                            <a href="/dashboard/file-uploads" class="nav-link pt-0 pb-0">
                                <span aria-hidden="true" class="icon-filemanager icon-2x float-left">&nbsp;</span>
                                <span class="d-none d-lg-block file-uploads float-right">&nbsp;File Uploads</span>
                            </a>
                        </li>
                        <li class="nav-item pl-3">
                            <a href="/dashboard/notifications" class="nav-link pt-0 pb-0">
                                <span aria-hidden="true" class="icon-draft2 icon-2x float-left"></span>
                                <span class="badge badge-danger counter float-right" style="display: none;">1</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown pl-3">
                            <a id="navbarDropdownMenuLink1" title="My Account" href="/dashboard" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle pt-0 pb-0">
                                <div class="username clearfix">
                                    <span class="float-left">{{$user_name}}</span>
                                    <span aria-hidden="true" class="icon-cog icon-2x float-right"></span>
                                </div>
                            </a>
                            <div aria-labelledby="navbarDropdownMenuLink1" class="dropdown-menu dropdown-menu-right hide">
                                <div class="dropdown-header">My Account</div>
                                <a href="/dashboard" tabindex="-1" class="dropdown-item p-3"><i class="icon-uniF006 text-muted mr-2"></i>My Dashboard</a>
                                <a href="/dashboard/profile" tabindex="-1" class="dropdown-item p-3"><i class="icon-webmail text-muted mr-2"></i>Update Profile</a>
                                <a href="/help-centre" target="_blank" tabindex="-1" class="dropdown-item p-3"><i class="icon-uniF014 text-muted mr-2"></i>Help Centre</a>
                                <p class="dropdown-divider"></p>
                                <a href="/dashboard/logout" tabindex="-1" class="dropdown-item p-3"><i class="icon-power text-muted mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>


        <div id="container">
            @if($load_msg != '')
                {{$load_msg}}
            @else
            {!! json_decode($livecycle_form) !!}
            @endif
        </div>

{{--        <script src="/dashboard/js/app.js?v=1.0"></script>--}}

        <script src="/dashboard/js/lc_application.js?qnyulc"></script>

{{--        <script src="//code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>--}}

{{--        <script src="//code.jquery.com/jquery-3.5.1.min.js"></script>--}}
{{--        <script src="//cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>--}}
{{--        <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>--}}
{{--        <script>--}}
{{--            $("#debug_close_btn").click(function(){--}}
{{--                $("#debug_box").css('height', '0px');--}}
{{--            });--}}
{{--            $("#debug_expand_btn").click(function(){--}}
{{--                $("#debug_box").css('height', '300px');--}}
{{--                $("#debug_expand_btn").hide();--}}
{{--                $("#debug_minimize_btn").show();--}}
{{--            });--}}
{{--            $("#debug_minimize_btn").click(function(){--}}
{{--                $("#debug_box").css('height', '65px');--}}
{{--                $("#debug_expand_btn").show();--}}
{{--                $("#debug_minimize_btn").hide();--}}
{{--            });--}}
{{--        </script>--}}
    </body>
</html>
