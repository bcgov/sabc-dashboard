<?php

namespace App\Http\Controllers;

use App\Declaration;
use App\DeclarationField;
use App\Http\Requests\AjaxRequest;
use App\SidePage;
use App\User;
use Auth;
use Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings', ['user' => Auth::user(), 'roles' => Auth::user()->roles, 'counter' => 0]);
    }

    /**
     * fetch the specified resource in storage.
     *
     * @param  AjaxRequest  $request
     * @param    $type
     * @return \Illuminate\Http\Response
     */
    public function fetch(AjaxRequest $request, $type)
    {
        $counter = 0;
        $finds = [];
        if ($type == 'declarations') {
            $validated = $request->validate([
                'dec_old_text' => 'required',
                'dec_new_text' => 'required',
                'dec_year' => 'required',
            ]);
            $old_text = $request->dec_old_text;
            $new_text = $request->dec_new_text;
            $declarations = Declaration::where('program_year', $request->dec_year)->get();
            foreach ($declarations as $dec) {
                foreach ($dec->fields as $field) {
                    $check = substr_count($field->field_value, $old_text);
                    if (substr_count($field->field_value, $old_text) > 0) {
                        $finds[] = ['name' => $dec->name, 'type' => $dec->type, 'field_id' => $field->id, 'field_text' => $field->field_value];
                    }
                }
            }
        } elseif ($type == 'side-pages') {
            $validated = $request->validate([
                'side_page_old_text' => 'required',
                'side_page_new_text' => 'required',
            ]);
            $old_text = $request->side_page_old_text;
            $new_text = $request->side_page_new_text;
            $side_pages = SidePage::where('status', 'active')->get();
            foreach ($side_pages as $page) {
                if (substr_count($page->left_side_draft, $old_text) > 0) {
                    $finds[] = ['page' => $page, 'ref' => 'left_side_draft', 'section' => 'Left Side Draft'];
                }
                if (substr_count($page->left_side, $old_text) > 0) {
                    $finds[] = ['page' => $page, 'ref' => 'left_side', 'section' => 'Left Side'];
                }
                if (substr_count($page->right_side_draft, $old_text) > 0) {
                    $finds[] = ['page' => $page, 'ref' => 'right_side_draft', 'section' => 'Right Side Draft'];
                }
                if (substr_count($page->right_side, $old_text) > 0) {
                    $finds[] = ['page' => $page, 'ref' => 'right_side', 'section' => 'Right Side'];
                }
            }
        }

        return Response::json(['finds' => $finds], 200); // Status code here
    }

    /**
     * declaration update the specified resource in storage.
     *
     * @param  AjaxRequest  $request
     * @param    $declarationField
     * @return \Illuminate\Http\Response
     */
    public function decUpdate(AjaxRequest $request, $declarationField)
    {
        $validated = $request->validate([
            'old_text' => 'required',
            'new_text' => 'required',
        ]);
        $declarationField = DeclarationField::find($declarationField);
        $new_text = str_replace($request->old_text, $request->new_text, $declarationField->field_value);
        $declarationField->field_value = $new_text;
        $declarationField->save();
        $declarationField = DeclarationField::find($declarationField->id);

        return Response::json(['result' => $declarationField], 200); // Status code here
    }

    /**
     * declaration update the specified resource in storage.
     *
     * @param  AjaxRequest  $request
     * @param    $sidePage
     * @return \Illuminate\Http\Response
     */
    public function sidePageUpdate(AjaxRequest $request, $sidePage)
    {
        $user = User::find(Auth::user()->id);

        $validated = $request->validate([
            'old_text' => 'required',
            'new_text' => 'required',
            'ref' => 'required',
        ]);
        $sidePage = SidePage::find($sidePage);
        $new_text = str_replace($request->old_text, $request->new_text, $sidePage[$request->ref]);
        $sidePage[$request->ref] = $new_text;

        $sidePage->uid = $user->uid;
        $sidePage->modified_by = $user->name;

        $sidePage->save();
        $sidePage = SidePage::find($sidePage->id);

        return Response::json(['result' => $sidePage], 200); // Status code here
    }
}
