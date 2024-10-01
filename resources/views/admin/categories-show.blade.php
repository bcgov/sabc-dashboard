@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <categories-edit old="{{ json_encode(Session::getOldInput()) }}" category="{{$category}}" errors="{{$errors}}"></categories-edit>
        @else
            <categories-edit old="" category="{{$category}}" errors=""></categories-edit>
        @endif

    </transition>
@endsection
