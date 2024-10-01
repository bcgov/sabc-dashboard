@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <alerts-new old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}"></alerts-new>
        @else
            <alerts-new old="" errors=""></alerts-new>
        @endif

    </transition>
@endsection
