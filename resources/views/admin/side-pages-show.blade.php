@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <side-pages-edit old="{{ json_encode(Session::getOldInput()) }}" page="{{$page}}" errors="{{$errors}}"></side-pages-edit>
        @else
            <side-pages-edit old="" page="{{$page}}" errors=""></side-pages-edit>
        @endif

    </transition>
@endsection
