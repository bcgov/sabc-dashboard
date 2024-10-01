@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <forms-new old="{{ json_encode(Session::getOldInput()) }}" errors="{{$errors}}"></forms-new>
        @else
            <forms-new old="" errors=""></forms-new>
        @endif

    </transition>
@endsection
