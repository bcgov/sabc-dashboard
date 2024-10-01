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
            <appendix-page errors="{{$errors}}" appno="{!! $application !!}" formguid="{!! $formGuid  !!}"></appendix-page>
        @else
            <appendix-page errors="" appno="{!! $application !!}" formguid="{!! $formGuid  !!}"></appendix-page>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
