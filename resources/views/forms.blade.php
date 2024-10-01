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
            <form-page errors="{{$errors}}"></form-page>
        @else
            <form-page errors=""></form-page>
        @endif
    </transition>

@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
