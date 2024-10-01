@extends('layouts.dashboard-no-sin')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>Appendix 1 Claim</h4>
        <hr class="mt-0"/>
    </div>
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <appendix1-no-sin-claim program_years="{{$program_years}}" py="{{$program_year}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></appendix1-no-sin-claim>
        @else
            <appendix1-no-sin-claim program_years="{{$program_years}}" py="{{$program_year}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></appendix1-no-sin-claim>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
