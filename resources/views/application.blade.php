@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <application-page appno="{!! $application !!}" resendError="{{ session('resend_error') }}"  resendSuccess="{{ session('resend_success') }}"></application-page>
    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
