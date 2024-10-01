@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <categories-new old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}"></categories-new>
        @else
            <categories-new old="" errors=""></categories-new>
        @endif

    </transition>
@endsection
