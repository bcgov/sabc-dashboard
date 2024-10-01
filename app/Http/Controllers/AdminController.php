<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
//use App\SidePage;

use Auth;
use Illuminate\Http\Request;
use Redirect;
//use Illuminate\Support\Str;
use Response;

/**
 * The standard log2 number of iterations for password stretching. This should
 * increase by 1 every Drupal version in order to counteract increases in the
 * speed and power of computers available to crack the hashes.
 */
const DRUPAL_HASH_COUNT = 15;

/**
 * The minimum allowed log2 number of iterations for password stretching.
 */
const DRUPAL_MIN_HASH_COUNT = 7;

/**
 * The maximum allowed log2 number of iterations for password stretching.
 */
const DRUPAL_MAX_HASH_COUNT = 30;

/**
 * The expected (and maximum) number of characters in a hashed password.
 */
const DRUPAL_HASH_LENGTH = 55;

class AdminController extends Aeit
{
    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminDashboard()
    {
        return view('admin.dashboard', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
//        return view('admin', ['user' => Auth::user()]);
    }


    /*
    * @param  \Illuminate\Http\Request  $request
    */
    public function createUser(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:users,name',
            'password' => 'required|min:8',
            'email' => 'email',
        ]);

        $next_uid = User::select('uid')->orderBy('uid', 'desc')->first();
        $old_uid = User::select('uid')->where('name', 'ilike', $request->name)->first();

        $user = new User();
        $user->uid = $next_uid->uid + 1;

        //if the user has an account recreate it with the same UID
        if (! is_null($old_uid)) {
            $user->uid = $old_uid->uid;
        }

        $user->name = $request->name;
        $user->email = strtolower($request->email);
        $user->password = $user->user_hash_password($request->password, DRUPAL_HASH_COUNT);
        $user->created = strtotime('now');
        $user->status = 1;
        $user->save();

        if ($request->roles !== null) {
            foreach ($request->roles as $role_name) {
                $role = Role::where('name', $role_name)->first();
                if (! is_null($role)) {
                    $user->roles()->attach($role);
                }
            }
        }
        return view('admin.users', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    public function newUser(Request $request)
    {
        return view('admin.users-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    public function showUsers(Request $request)
    {
        return view('admin.users', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * @param  User  $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showUser($user_id)
    {
        $user = User::where('uid', $user_id)->with('roles')->first();

        return view('admin.users-show', ['user' => $user, 'roles' => Auth::user()->roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param    $user
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $user_id)
    {
        $user = User::where('uid', $user_id)->with('roles')->first();
        if (! is_null($user)) {
            $this->validate($request, [
                'name' => 'required|unique:users,name,'.$user->uid.',uid|min:4',
                'status' => 'required|in:0,1',
            ]);

            if ($request->email !== null) {
                $this->validate($request, [
                    'email' => 'email',
                ]);
                $email = str_replace(' ', '', $request->email);
                $user->email = $email;
            }

            if ($request->password !== null) {
                $this->validate($request, [
                    'password' => 'required|string|between:8,20|regex:/^.*(?=.*[a-zA-Z])(?=.*[^a-zA-Z\d\s])(?=.*[0-9])(?=.*[\d\x]).*$/',
                    'confirm_password' => 'required|string|same:password',
                ]);
                $user->password = $user->user_hash_password($request->password, DRUPAL_HASH_COUNT);
            }

            //adds code to update user

            $user->name = $request->name;
            $user->status = intval($request->status);
            $user->save();

            $user->roles()->detach();
            if ($request->roles !== null) {
                foreach ($request->roles as $role_name) {
                    $role = Role::where('name', $role_name)->first();
                    if (! is_null($role)) {
                        $user->roles()->attach($role);
                    }
                }
            }
        }

        return redirect('/admin/users');
    }

    public function fetchUsers(Request $request)
    {
        $users = User::select('id', 'name', 'uid', 'status', 'updated_at')->distinct('uid');
        if ($request->username !== null) {
            $users = $users->where('name', 'ilike', '%'.$request->username.'%');
        }

        if ($request->status !== null) {
            $users = $users->where('status', $request->status);
        }

        if ($request->role !== null) {
            $users = $users->with('roles', function ($q) {
            $q->select('name');
            })->whereHas('roles', function ($q) use ($request) {
            $q->where('name', $request->role);
            });
        } else {
            $users = $users->with('roles', function ($q) {
            $q->select('name');
            });
        }

        $users = $users->orderBy('uid', 'desc')->paginate(50);

        return Response::json(['users' => $users], 200); // Status code here
    }
}
