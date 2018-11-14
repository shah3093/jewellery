<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\ProductCategory;
use App\Customefield;
use App\Asset;
use App\Menu;
use App\Page;
use App\CustomHelper\Fileupload;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller {

    function index() {
        return view('admin/menu/index');
    }

    function listing() {
        $results = DB::table('menus')->orderBy('created_at', 'desc')->get();
        return view('admin/menu/listing', ['results' => $results]);
    }

    function formview($id = null) {
        $data['pages'] = DB::table('pages')->orderBy('created_at', 'desc')->get();
        $data['menus'] = DB::table('menus')->orderBy('created_at', 'desc')->get();
        if ($id) {
            $data['result'] = Menu::find($id);
            return view('admin/menu/edit', $data);
        } else {
            return view('admin/menu/add', $data);
        }
    }

    function addformdb(Request $request, $menuid = null) {
        $e_messages = [
            'fData.name' => 'Name is required',
            'fData.menu_url' => 'Content is required'
        ];

        $validator = Validator::make($request->all(), [
                    'fData.name' => 'required',
                    'fData.menu_url' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if ($menuid) {
                $fromdata = $request->input('fData');
                Menu::where('id', $menuid)->update($fromdata);
                return response()->json(["DONE"]);
            } else {
                $fromdata = $request->input('fData');
                Menu::create($fromdata);
                return response()->json(["DONE"]);
            }
        }
    }

    function delete($id) {
        $result = Asset::find($id);
        if ($result) {
            Storage::delete('public/' . $result->file_name);
        }
        Asset::destroy($id);
    }

    public function menutype(Request $request) {
        $type = $request->input('type');
        if ($type != "Custom") {
            $tmp = stripslashes($type);
            $data['type'] = $tmp = str_replace("App", "", $type);
            $data['type'] = stripslashes($tmp);
            $data['results'] = $type::all();
        } else {
            $data['results'] = "custom";
        }
        return view('admin/menu/pagetype', $data);
    }

}
