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
            <dashboard-register old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}" env="{{$env}}"></dashboard-register>
        @else
            <dashboard-register old="" errors="" env="{{$env}}"></dashboard-register>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
