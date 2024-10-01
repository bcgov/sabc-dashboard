@extends('app_support.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-10">
                <div class="card">
                    <div class="card-header"><strong>{{$user->name}}</strong> Welcome to Application Support</div>
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

                            @error('guid')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                    </div>
                </div>
                <hr/>

                <div class="card">
                    <div class="card-header">SABC Login</div>
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

                        <form action="/dashboard/app_support/login_sabc_user" method="post" id="app_support_sabc_login" accept-charset="UTF-8">
                            @csrf
                            <div class="form-group">
                                <label for="environment">Environment</label>
                                <select class="form-control" name="environment" id="login_environment">
                                    <option value="DEV">DEV</option>
                                    <option value="UAT">UAT</option>
                                    <option value="STUDENTAIDBC">PROD</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-user-id">User ID</label>
                                <input autocomplete="off" type="text" id="edit-user-id" name="user_id" value="" size="60" maxlength="32" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="edit-password">Password</label>
                                <input autocomplete="off" type="password" id="edit-password" name="password" value="" size="60" maxlength="32" class="form-control">
                            </div>

                            @error('username')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
