@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <admin-users-new errors="{{$errors}}"></admin-users-new>
        @else
            <admin-users-new errors=""></admin-users-new>
        @endif

    </transition>
@endsection
