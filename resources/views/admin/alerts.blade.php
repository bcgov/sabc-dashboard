
@extends('admin.layouts.main')

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        <alerts></alerts>
    </transition>
@endsection
