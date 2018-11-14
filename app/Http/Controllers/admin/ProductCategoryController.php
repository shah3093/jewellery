<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\ProductCategory;
use App\Customefield;
use App\Asset;
use App\CustomHelper\Fileupload;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Image;

class ProductCategoryController extends Controller {

    function index() {
        return view('admin/productcategory/index');
    }

    function listing() {
        $results = DB::table('product_categories')->orderBy('created_at', 'desc')->get();
        return view('admin/productcategory/listing', ['results' => $results]);
    }

    function formview($id = null) {
        if ($id) {
            $data['result'] = ProductCategory::find($id);
            $condition = array(
                'table_name' => "product_categories",
                'table_id' => $id
            );
            $data['files'] = Asset::where($condition)->get();
            $data['icon'] = Asset::where(['table_name' => "product_categories", 'table_id' => $id, 'type' => "ICON"])->first();
            $data['topicimg'] = Asset::where(['table_name' => "product_categories", 'table_id' => $id, 'type' => "TOPICIMAGE"])->first();
            return view('admin/productcategory/edit', $data);
        } else {
            return view('admin/productcategory/add');
        }
    }

    public function filefrom($type, $cnt) {
        if ($type == "img") {
            return view("admin/layouts/imgeform", ['cnt' => $cnt]);
        } else if ($type == "file") {
            return view("admin/layouts/fileform", ['cnt' => $cnt]);
        } else {
            return view("admin/layouts/fieldform", ['cnt' => $cnt]);
        }
    }

    function addformdb(Request $request, $categoryid = null) {
        $e_messages = [
            'fData.name' => 'Category is required',
            'fData.slug' => 'Slug is required'
        ];

        $validator = Validator::make($request->all(), [
                    'fData.name' => 'required',
                    'fData.slug' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if ($categoryid) {

                $fromdata = $request->input('fData');
                ProductCategory::where('id', $categoryid)->update($fromdata);

                if ($categoryid) {

                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $categoryid, "product_categories");
                    }
                    ///END FOR IMAGE /////
                    ///
                    ///  ///  ///START FOR ICON/////
                    if ($request->hasFile('font_icon')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('font_icon')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('font_icon')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $datafile = array(
                                'table_name' => 'product_categories',
                                'table_id' => $categoryid,
                                'file_name' => $path,
                                'type' => "ICON"
                            );
                            $assetsfiled = Asset::create($datafile);
                        }
                    }
                    if ($request->input('Icon.caption') != NULL) {
                        $iconcode = $request->input('Icon.caption');
                        $datafile = array(
                            'table_name' => 'product_categories',
                            'table_id' => $categoryid,
                            'caption' => $iconcode,
                            'type' => "ICON"
                        );
                        $assetsfiled = Asset::create($datafile);
                    }
                    ///END FOR ICON /////
                    /// START FOR TOPICIMAGE ///
                    if ($request->hasFile('topicimage')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('topicimage')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('topicimage')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $caption = $request->input('Topic.caption');
                            $datafile = array(
                                'table_name' => 'product_categories',
                                'table_id' => $categoryid,
                                'file_name' => $path,
                                'type' => "TOPICIMAGE",
                                'caption' => $caption,
                            );
                            $assetsfiled = Asset::create($datafile);

                            ///Image resize
                            $imageName = trim(str_replace('public/assets/', '', $path));
                            Fileupload::imageResize($imageName, 500, 400);
                        }
                    }
                    /// END FOR TOPICIMAGE ////

                    return response()->json(["DONE"]);
                } else {
                    return response()->json(["Someting went wrong"]);
                }
            } else {
                $fromdata = $request->input('fData');
                $categoryid = ProductCategory::create($fromdata)->id;
                if ($categoryid) {
                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $categoryid, "product_categories");
                    }
                    ///END FOR IMAGE /////
                    ///  ///START FOR ICON/////
                    if ($request->hasFile('font_icon')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('font_icon')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('font_icon')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $datafile = array(
                                'table_name' => 'product_categories',
                                'table_id' => $categoryid,
                                'file_name' => $path,
                                'type' => "ICON"
                            );
                            $assetsfiled = Asset::create($datafile);
                        }
                    }
                    if ($request->has('Icon') != NULL) {
                        $iconcode = $request->input('Icon.caption');
                        $datafile = array(
                            'table_name' => 'product_categories',
                            'table_id' => $categoryid,
                            'caption' => $iconcode,
                            'type' => "ICON"
                        );
                        $assetsfiled = Asset::create($datafile);
                    }
                    ///END FOR ICON /////
                    /// START FOR TOPICIMAGE ///
                    if ($request->hasFile('topicimage')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('topicimage')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('topicimage')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $caption = $request->input('Topic.caption');
                            $datafile = array(
                                'table_name' => 'product_categories',
                                'table_id' => $categoryid,
                                'file_name' => $path,
                                'type' => "TOPICIMAGE",
                                'caption' => $caption,
                            );
                            $assetsfiled = Asset::create($datafile);

                            ///Image resize
                            $imageName = trim(str_replace('public/assets/', '', $path));
                            Fileupload::imageResize($imageName, 500, 400);
                        }
                    }
                    /// END FOR TOPICIMAGE ////

                    return response()->json(["DONE"]);
                } else {
                    return response()->json(["Someting went wrong"]);
                }
            }
        }
    }

    public function slugcreating(Request $request) {
        $pagetitle = $request["name"];

        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = ProductCategory::where('slug', $data)->first();

        if (!$result) {
            $tmp = explode(" ", $pagetitle);
            $tmp = implode("-", $tmp);
            return $tmp;
        } else {
            $tmp = explode(" ", $pagetitle);
            $data = implode("-", $tmp);
            $i = 1;
            while ($result) {
                $tmp = explode(" ", $pagetitle);
                $data = implode("-", $tmp);
                $data = $data . "-" . $i;
                $result = ProductCategory::where('slug', $data)->first();
                $i++;
            }
            return $data;
        }
    }

    public function slugchecking(Request $request) {
        $pagetitle = $request["title"];
        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = ProductCategory::where('slug', $data)->first();
        if ($result) {
            echo "WRONG";
            return;
            return response()->json(["WRONG"]);
        } else {
            return response()->json([$data]);
        }
    }

    public function deletefile($id) {
        $result = Asset::find($id);
        if ($result) {
            if ($result->type != "ICON") {
                Storage::delete('public/' . $result->file_name);
            }
            Asset::destroy($id);
        } else {
            Customs_field::destroy($id);
        }
    }

    function delete($categoryid) {
        $conditions = array(
            'table_name' => "product_categories",
            "table_id" => $categoryid
        );
        $assets = Asset::where($conditions)->get();
        foreach ($assets as $asset) {
            Storage::delete('public/' . $asset->file_name);
        }
        $deleteasset = Asset::where($conditions)->delete();
        ProductCategory::destroy($categoryid);
    }

}
