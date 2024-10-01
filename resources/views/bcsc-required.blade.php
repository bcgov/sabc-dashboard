@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <bcsc-verification-required></bcsc-verification-required>

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
