@extends('layouts.auth')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            @if(Session::has('password-reset'))
                <dashboard-login errors="{{$errors}}" userid="{{ old('user_id') }}" env="{{$env}}" :passwordreset="true"></dashboard-login>
            @else
                <dashboard-login errors="{{$errors}}" userid="{{ old('user_id') }}" env="{{$env}}" :passwordreset="false"></dashboard-login>
            @endif

        @else
            <dashboard-login errors="" userid="{{ old('user_id') }}" env="{{$env}}" :passwordreset="false"></dashboard-login>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
