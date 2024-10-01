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
            <form-start-page errors="{{$errors}}"></form-start-page>
        @else
            <form-start-page errors=""></form-start-page>
        @endif
    </transition>

@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
