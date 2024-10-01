@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <declarations-edit-edit old="{{ json_encode(Session::getOldInput()) }}" declaration="{{$declaration}}" errors="{{$errors}}"></declarations-edit-edit>
        @else
            <declarations-edit old="" declaration="{{$declaration}}" errors=""></declarations-edit>
        @endif

    </transition>
@endsection
