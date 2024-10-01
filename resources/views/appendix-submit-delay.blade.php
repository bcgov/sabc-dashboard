@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left"></aside-component>
@endsection

@section('content')
    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <appendix-submit-success-page app_id="{{$app_id}}" appendix_type="{{$appendix_type}}" document_guid="{{$document_guid}}" load_msg="{{$load_msg}}" load_status="{{$load_status}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></appendix-submit-success-page>
        @else
            <appendix-submit-success-page app_id="{{$app_id}}" appendix_type="{{$appendix_type}}" document_guid="{{$document_guid}} "load_msg="{{$load_msg}}" load_status="{{$load_status}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></appendix-submit-success-page>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right"></aside-component>
@endsection
