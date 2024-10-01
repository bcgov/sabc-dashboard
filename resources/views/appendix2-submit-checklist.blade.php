@extends('layouts.dashboard')

@section('aside_left')
    <aside-component section="left" customurl="/dashboard/appendix-submit-checklist/appendix2/*/*/*/*"></aside-component>
@endsection

@section('content')
    <div class="taskbar">
        <h4>Appendix 2 Agreement</h4>
        <hr class="mt-0"/>
    </div>

    <transition name="fade" mode="out-in">
        <!-- route outlet -->
        <!-- component matched by the route will render here -->
        <!--<router-view></router-view>-->
        @if (isset($errors) && count($errors))
            <appendix2-submit-checklist-page program_year="{{$program_year}}" app_id="{{$app_id}}" document_guid="{{$document_guid}}" declaration="{{$declaration}}" checklist="{{$checklist}}" load_msg="{{$load_msg}}" load_status="{{$load_status}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors="{{$errors}}"></appendix2-submit-checklist-page>
        @else
            <appendix2-submit-checklist-page program_year="{{$program_year}}" app_id="{{$app_id}}" document_guid="{{$document_guid}}" declaration="{{$declaration}}" checklist="{{$checklist}}" load_msg="{{$load_msg}}" load_status="{{$load_status}}" submit_status="{{$submit_status}}" submit_msg="{{$submit_msg}}" errors=""></appendix2-submit-checklist-page>
        @endif

    </transition>
@endsection

@section('aside_right')
    <aside-component section="right" customurl="/dashboard/appendix-submit-checklist/appendix2/*/*/*/*"></aside-component>
@endsection
