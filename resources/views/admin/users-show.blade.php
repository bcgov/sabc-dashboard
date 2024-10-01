@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <admin-users-edit user="{{$user}}" errors="{{$errors}}"></admin-users-edit>
        @else
            <admin-users-edit user="{{$user}}" errors=""></admin-users-edit>
        @endif

    </transition>
@endsection
