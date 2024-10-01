@extends('app_support.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-10">
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


                        <h4 class="pb-3">PROFILE</h4>
                        <table class="table table-dark">
                            <tbody>
                                <?php

                                $profile = json_decode($profile, true);

                                if(is_array($profile) && !empty($profile)){
                                    $clmn = 0;
                                    foreach ($profile['userProfile'] as $key => $value){
                                        if(!is_array($value) && $key != 'SIN' && $key != 'userGUID'){
                                            if($clmn == 2){
                                                $clmn = 0;
                                            }

                                            if($clmn == 0){
                                                if(is_bool($value)){
                                                    echo '<tr><td class="bg-light text-dark text-right">' . $key . '</td><td>' . ($value == true ? 'TRUE' : 'FALSE') . '</td>';
                                                }else{
                                                    echo '<tr><td class="bg-light text-dark text-right">' . $key . '</td><td>' . $value . '</td>';
                                                }
                                            }
                                            if($clmn == 1){
                                                if(is_bool($value)){
                                                    echo '<td class="bg-light text-dark text-right">' . $key . '</td><td>' . ($value == true ? 'TRUE' : 'FALSE') . '</td></tr>';
                                                }else{
                                                    echo '<td class="bg-light text-dark text-right">' . $key . '</td><td>' . $value . '</td></tr>';
                                                }
                                            }

                                            $clmn++;
                                        }
                                    }
                                    if($clmn != 2){
                                        echo "</tr>";
                                    }

                                    echo '<tr><td class="bg-light text-dark text-right">Status</td><td>' . ($profile['status'] == true ? 'TRUE' : 'FALSE') . '</td><td class="bg-light text-dark text-right">Is BCSC Account</td><td>' . ($profile['hasBcscAccount'] == true ? 'TRUE' : 'FALSE') . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
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
