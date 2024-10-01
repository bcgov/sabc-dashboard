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
            <appendix2-claim program_years="{{$program_years}}" access_code="{{$access_code}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></appendix2-claim>
        @else
            <appendix2-claim program_years="{{$program_years}}" access_code="{{$access_code}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></appendix2-claim>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
