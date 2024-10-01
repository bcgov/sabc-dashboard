@extends('layouts.dashboard')
<script src="/dashboard/js/dropzone.js"></script>
@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <file-uploads></file-uploads>
    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
