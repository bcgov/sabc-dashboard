@extends('app_support.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><strong>{{$user_guid}}</strong> ({{$env}}) <a href="/dashboard/app_support/access" class="btn btn-success btn-sm float-right">Access Account</a></div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <h4 class="pb-3">APPENDIX for Application {{$app_number}}</h4>
                            <app-support-appendix-detail appnumber="{{$app_number}}"></app-support-appendix-detail>

                            {{--                        <pre>--}}
{{--                            <?php var_dump($profile); ?>--}}
{{--                        </pre>--}}
{{--                        <h4>VERIFY</h4>--}}
{{--                        <pre>--}}
{{--                            {{$verify}}--}}
{{--                        </pre>--}}
                        <hr/>
                            <form action="/dashboard/app_support/find_user" method="post" id="app_support_access" accept-charset="UTF-8">
                                @csrf
                                <div class="form-group">
                                    <label for="environment">Environment</label>
                                    <select class="form-control" name="environment" id="environment">
                                        <option value="DEV">DEV</option>
                                        <option value="UAT">UAT</option>
                                        <option value="STUDENTAIDBC">PROD</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="edit-user-guid">User GUID</label>
                                    <input autocomplete="off" type="text" id="edit-user-guid" name="user_guid" value="" size="60" maxlength="32" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
