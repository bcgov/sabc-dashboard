@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <alerts-edit-edit old="{{ json_encode(Session::getOldInput()) }}" alert="{{$alert}}" errors="{{$errors}}"></alerts-edit-edit>
        @else
            <alerts-edit old="" alert="{{$alert}}" errors=""></alerts-edit>
        @endif

    </transition>
@endsection
