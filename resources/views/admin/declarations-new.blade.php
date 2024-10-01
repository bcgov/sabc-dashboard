@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <declarations-new old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}"></declarations-new>
        @else
            <declarations-new old="" errors=""></declarations-new>
        @endif

    </transition>
@endsection
