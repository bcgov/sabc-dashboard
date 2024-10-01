@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <student-apply></student-apply>
    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
