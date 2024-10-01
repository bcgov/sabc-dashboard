@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>Confirm Profile</h4>
        <hr class="mt-0"/>
    </div>

    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <application-apply school="{{$school}}" program_years="{{$program_years}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></application-apply>
        @else
            <application-apply school="{{$school}}" program_years="{{$program_years}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></application-apply>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
