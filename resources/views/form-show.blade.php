@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>SHOW FORM <a href="/dashboard/appeal-forms" class="btn btn-link btn-sm float-right mt-2">Back to Appeals &amp; Forms</a></h4>
        <hr class="mt-0"/>
    </div>

    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <form-show-page form="{{$form}}" errors="{{$errors}}"></form-show-page>
        @else
            <form-show-page form="{{$form}}" errors=""></form-show-page>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
