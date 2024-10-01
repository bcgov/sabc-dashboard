@extends('admin.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        Welcome: {{$user->name}} (
                        @foreach($roles as $role)
                            {{ $role->name }}, 
                        @endforeach
                        )
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
