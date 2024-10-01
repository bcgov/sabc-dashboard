@extends('layouts.auth')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <dashboard-bcsc-forgot errors="{{$errors}}" old="{{ json_encode(Session::getOldInput()) }}" env="{{$env}}" data="{{json_encode($data)}}" step="{{$step}}"></dashboard-bcsc-forgot>
        @else
            <dashboard-bcsc-forgot errors="" old="{{ old('user_id') }}" env="{{$env}}" data="{{json_encode($data)}}" step="{{$step}}"></dashboard-bcsc-forgot>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
