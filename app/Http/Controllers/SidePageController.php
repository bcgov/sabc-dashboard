<?php

namespace App\Http\Controllers;

use App\SidePage;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Response;

class SidePageController extends Controller
{
    public function fetchSidePages(Request $request)
    {
        return Response::json(['pages' => SidePage::orderBy('updated_at', 'desc')->get()], 200); // Status code here
    }

    public function fetchDashboardSides(Request $request)
    {
        $path = $request->path;
        $sides = SidePage::select('left_side', 'right_side', 'status')->where('url', 'ilike', $path)->first();
        if (is_null($sides)) {
            $sides = SidePage::select('left_side', 'right_side', 'status')->where('url', 'ilike', '/default')->first();
        }

        $amount_borrowed = false;
        if (Str::lower($path) == '/dashboard/') {
            if (Auth::check()) {
                $amount_borrowed = Cache::get(session(env('GUID_SESSION_VAR')).env('GUID_SESSION_VAR9'), null);
                if (is_null($amount_borrowed)) {
                    $aeit = new Aeit();

                    $aeit->uid = session(env('GUID_SESSION_VAR'));
                    $aeit->user = User::where('id', Auth::user()->id)->with('roles')->first();

                    $amount_borrowed = $aeit->fnGetEstAmountBorrowed();
                    //cache data only if there is no soap error
                    if (! isset($amount_borrowed['error'])) {
                        //Cache::put(session(env('GUID_SESSION_VAR')) . env('GUID_SESSION_VAR9'), $amount_borrowed, now()->addMinutes(env('SESSION_LIFETIME'))); //put it in the cache for the length of the session
                    } else {
                        $amount_borrowed = false;
                    }

                    //$amount_borrowed = 555;
                    if (! empty($amount_borrowed) && ! is_null($sides)) {
                        $sides->right_side .= $this->buildAmountBorrowed($amount_borrowed);
                    }
                }
            }
        }

        return Response::json(['sidePage' => $sides], 200); // Status code here
    }

    private function buildAmountBorrowed($estAmtBorrowed)
    {
        if (! empty($estAmtBorrowed)) {
            $widget = '<div style="position: absolute; width: 100%; bottom: 0;">';
            $widget .= '<hr class="small" />';
            $widget .= '<div class="p-3">';
            $widget .= '<a data-toggle="modal" data-target="#loanAuthorizedModal">';
            $widget .= '<h6 class="uppercase">Loan authorized to date</h6>';
            $widget .= '<p><span class="h5 text-primary">$'.$estAmtBorrowed.'</span> <i class="icon-opennewwindow"></i></p>';
            $widget .= '</a>';
            $widget .= '</div>';

            $widget .= '<div class="modal fade" id="loanAuthorizedModal" tabindex="-1" role="dialog" aria-labelledby="loanAuthorizedModalLabel" aria-hidden="true">';
            $widget .= '<div class="modal-dialog">';
            $widget .= '<div class="modal-content">';

            $widget .= '<div class="modal-header">';
            $widget .= '<h5 class="modal-title">Loan authorized to date</h5>';
            $widget .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
            $widget .= '</div>';

            $widget .= '<div class="modal-body">';
            $widget .= '<p class="h5">Loan authorized to date is: <span class="h1 text-success">$'.$estAmtBorrowed.'</span></p>';
            $widget .= '<h6 class="uppercase">Notes:</h6>';
            $widget .= '<ul class="disc">';
            $widget .= '<li>This number is an <em class="highlight">estimate</em> of all Canada and B.C. student loans you have borrowed as issued by StudentAidBC.</li><li>This total does <strong>not</strong> include any payments you may have made.</li><li>You can get the exact amount of your outstanding loan from the <a href="https://www.csnpe-nslsc.canada.ca/" target="_blank">National Student Loan Service Centre</a>.</li>';
            $widget .= '</ul>';
            $widget .= '<hr class="small" />';
            $widget .= '</div>';

            $widget .= '</div>';
            $widget .= '</div>';
            $widget .= '</div>';
            $widget .= '</div>';
        } else {
            $widget = null;
        }

        return $widget;
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
    public function sidePages()
    {
        return view('admin.side-pages', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sidePagesNew(Request $request)
    {
        return view('admin.side-pages-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
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
            'page_name' => 'required',
            'page_path' => 'required|unique:side_pages,url',
            'left_side' => 'required',
            'right_side' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $name = Str::upper($request->page_name);
        $url = Str::lower(Str::start($request->page_path, '/'));

        $page = new SidePage();
        $page->name = $name;
        $page->url = $url;
        if ($request->status == 'draft') {
            $page->left_side_draft = $request->left_side;
            $page->right_side_draft = $request->right_side;
        }
        $page->left_side = $request->left_side;
        $page->right_side = $request->right_side;
        $page->uid = $user->uid;
        $page->modified_by = $user->name;
        $page->status = $request->status;
        $page->save();

        return redirect('/admin/side-pages');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SidePage  $sidePage
     * @return \Illuminate\Http\Response
     */
    public function show(SidePage $sidePage)
    {
        return view('admin.side-pages-show', ['page' => $sidePage, 'roles' => Auth::user()->roles]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SidePage  $sidePage
     * @return \Illuminate\Http\Response
     */
    public function edit(SidePage $sidePage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SidePage  $sidePage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SidePage $sidePage)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'page_name' => 'required',
            'page_path' => 'required|unique:side_pages,url,'.$sidePage->id,
            'left_side' => 'required',
            'right_side' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        $name = Str::upper($request->page_name);
        $url = Str::lower(Str::start($request->page_path, '/'));

        //$page = new SidePage();
        $sidePage->name = $name;
        $sidePage->url = $url;
        if ($request->status == 'draft') {
            $sidePage->left_side_draft = $request->left_side;
            $sidePage->right_side_draft = $request->right_side;
        }
        $sidePage->left_side = $request->left_side;
        $sidePage->right_side = $request->right_side;
        $sidePage->uid = $user->uid;
        $sidePage->modified_by = $user->name;
        $sidePage->status = $request->status;
        $sidePage->save();

        return redirect('/admin/side-pages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SidePage  $sidePage
     * @return \Illuminate\Http\Response
     */
    public function destroy(SidePage $sidePage)
    {
        //
    }
}
