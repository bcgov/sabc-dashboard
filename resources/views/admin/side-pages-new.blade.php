@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <side-pages-new old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}"></side-pages-new>
        @else
            <side-pages-new old="" errors=""></side-pages-new>
        @endif

    </transition>
@endsection
