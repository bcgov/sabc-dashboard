@extends('layouts.auth')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>Create Account</h4>
        <hr class="mt-0"/>
    </div>
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <dashboard-create-bcsc-user old="{{ json_encode(Session::getOldInput()) }}" saml="{{$saml}}" role="{{$role}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></dashboard-create-bcsc-user>
        @else
            <dashboard-create-bcsc-user old="" saml="{{$saml}}" role="{{$role}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></dashboard-create-bcsc-user>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
