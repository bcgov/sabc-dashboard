@extends('layouts.dashboard-no-sin')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4></h4>
        <hr class="mt-0"/>
    </div>

    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <no-sin-declaration-page appendix_type="{{$appendix_type}}" app_id="{{$application_number}}" document_guid="{{$document_guid}}"></no-sin-declaration-page>

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
