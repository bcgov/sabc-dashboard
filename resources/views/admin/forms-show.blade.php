@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <forms-edit old="{{ json_encode(Session::getOldInput()) }}" form="{{$form}}" errors="{{$errors}}"></forms-edit>
        @else
            <forms-edit old="" form="{{$form}}" errors=""></forms-edit>
        @endif

    </transition>
@endsection
