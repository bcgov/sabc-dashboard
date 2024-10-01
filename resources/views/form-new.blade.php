@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>START NEW FORM <a href="/dashboard/appeal-forms/new" class="btn btn-link btn-sm float-right mt-2">Back</a></h4>
        <hr class="mt-0"/>
    </div>

    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <form-new-page uuid="{{$uuid}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></form-new-page>
        @else
            <form-new-page uuid="{{$uuid}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></form-new-page>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
