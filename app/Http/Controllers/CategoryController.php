<?php

namespace App\Http\Controllers;

use App\FormCategory;
use App\Http\Requests\AjaxRequest;
use Auth;
use Illuminate\Http\Request;
use Response;

class CategoryController extends Aeit
{
    public function fetchAll(AjaxRequest $request)
    {
        return Response::json(['categories' => FormCategory::where('status', 1)->with('forms')->orderBy('weight', 'asc')->get()], 200); // Status code here
    }

    public function fetchCategories(AjaxRequest $request)
    {
        return Response::json(['categories' => FormCategory::where('status', 1)->orderBy('weight', 'asc')->get()], 200); // Status code here
    }

    public function adminFetchCats(AjaxRequest $request)
    {
        return Response::json(['categories' => FormCategory::orderBy('updated_at', 'desc')->orderBy('weight', 'asc')->get()], 200); // Status code here
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminCats()
    {
        return view('admin.categories', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Display the dashboard page to a logged in admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminCatNew(Request $request)
    {
        return view('admin.categories-new', ['user' => Auth::user(), 'roles' => Auth::user()->roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AjaxRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function adminEditWeight(AjaxRequest $request, FormCategory $category, $weight)
    {
        $weight = intval($weight);

        $category->weight = $weight;
        $category->save();

        return Response::json(['category' => $category], 200); // Status code here
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        if (is_null($request->program_year)) {
            $request->merge([
                'program_year' => '-',
            ]);
        }
        $cat = new FormCategory();
        $cat->name = $request->name;
        $cat->long_name = $request->long_name;
        $cat->program_year = str_replace(' ', '', $request->program_year);
        $cat->status = $request->status == 'active' ? 1 : 0;
        $cat->save();

        return redirect('/admin/categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FormCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function adminShow(FormCategory $category)
    {
        $category = FormCategory::where('id', $category->id)->with('forms')->first();

        return view('admin.categories-show', ['category' => $category, 'roles' => Auth::user()->roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FormCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function adminUpdate(Request $request, FormCategory $category)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:draft,active',
        ]);

        if (is_null($request->program_year)) {
            $request->merge([
                'program_year' => '-',
            ]);
        }
        $category->name = $request->name;
        $category->long_name = $request->long_name;
        $category->program_year = str_replace(' ', '', $request->program_year);
        $category->status = $request->status == 'active' ? 1 : 0;
        $category->save();

        return redirect('/admin/categories');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FormCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function adminDelete(Request $request, FormCategory $category)
    {
        $category->delete();

        return redirect('/admin/categories');
    }
}
