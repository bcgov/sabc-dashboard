<?php

namespace App\Http\Controllers;

use App\Alert;
use App\User;
use Auth;
use Carbon\Carbon as Carbon;
use Illuminate\Http\Request;
use Response;

class AlertController extends Controller
{
    public function fetchAlerts(Request $request)
    {
        return Response::json(['alerts' => Alert::orderBy('updated_at', 'desc')->get()], 200); // Status code here
    }

    public function fetchDashboardAlerts(Request $request)
    {
        $pages = $request->target;
        $pages = filter_var($pages, FILTER_SANITIZE_STRING);

//        $alerts = Alert::select('body', 'type', 'start_time', 'end_time', 'status', 'pages', 'dismissible', 'order_index', 'disable_page')
//            ->where('status', 'active')
//            ->where('pages', $pages)
//            ->orWhere('pages', 'all')
//            ->where('start_time', '<=', \DB::raw('NOW()'))
//            ->where('end_time', '>', \DB::raw('NOW()'))
//            ->orderBy('order_index', 'asc')
//            ->get();
        $alerts = Alert::select('body', 'type', 'start_time', 'end_time', 'status', 'pages', 'dismissible', 'order_index', 'disable_page')
            ->where('status', 'active')
            ->where('start_time', '<=', Carbon::now()->toDateTimeString())
            ->where('end_time', '>', Carbon::now()->toDateTimeString())
            ->whereRaw(\DB::raw("(pages = '$pages' OR pages = 'all')"))
            ->orderBy('order_index', 'asc')
            ->get();

        return Response::json(['alerts' => $alerts, 'current_time' => date('Y-m-d H:i:s', strtotime('now'))], 200); // Status code here
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alerts(Request $request)
    {
        return view('admin.alerts', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alertsNew(Request $request)
    {
        return view('admin.alerts-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'body' => 'required',
            'pages' => 'required',
            'dismissible' => 'required',
            'order_index' => 'required',
            'start_time' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $alert = new Alert();
        $alert->name = $request->name;
        $alert->type = $request->type;
        $alert->body = $request->body;
        $alert->pages = $request->pages;
        $alert->start_time = date('Y-m-d H:i:s', strtotime($request->start_time));
        $alert->end_time = ! isset($request->end_time) ? null : date('Y-m-d H:i:s', strtotime($request->end_time));
        $alert->disable_page = ! isset($request->disable_page) ? false : $request->disable_page;
        $alert->order_index = $request->order_index;
        $alert->dismissible = ! isset($request->dismissible) ? false : $request->dismissible;
        $alert->uid = $user->uid;
        $alert->modified_by = $user->name;
        $alert->status = $request->status;
        $alert->save();

        return redirect('/admin/alerts');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function show(Alert $alert)
    {
        return view('admin.alerts-show', ['alert' => $alert, 'roles' => Auth::user()->roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alert $alert)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'body' => 'required',
            'pages' => 'required',
            'dismissible' => 'required',
            'order_index' => 'required',
            'start_time' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $alert->name = $request->name;
        $alert->type = $request->type;
        $alert->body = $request->body;
        $alert->pages = $request->pages;

        $alert->start_time = date('Y-m-d H:i:s', strtotime($request->start_time));
        $alert->end_time = ! isset($request->end_time) ? null : date('Y-m-d H:i:s', strtotime($request->end_time));
        $alert->disable_page = ! isset($request->disable_page) ? false : $request->disable_page;
        $alert->order_index = $request->order_index;
        $alert->dismissible = ! isset($request->dismissible) ? false : $request->dismissible;
        $alert->uid = $user->uid;
        $alert->modified_by = $user->name;
        $alert->status = $request->status;
        $alert->save();

        return redirect('/admin/alerts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Alert  $alert
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alert $alert)
    {
        //
    }
}
