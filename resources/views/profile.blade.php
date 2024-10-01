@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <profile submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></profile>
        @else
            <profile submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></profile>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
