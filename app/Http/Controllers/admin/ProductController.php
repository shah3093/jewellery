<?php

namespace App\Http\Controllers\admin;

use App\Asset;
use App\CustomHelper\Fileupload;
use App\Customefield;
use App\Product;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller {

    function index() {
        return view('admin/product/index');
    }

    function listing() {
        $results = Product::with('productcategory')->get();
        return view('admin/product/listing', ['results' => $results]);
    }

    function formview($id = null) {
        $categories = $data['categories'] = ProductCategory::where(['status' => 1])->get();
        if ($id) {
            $result = Product::find($id);
            $condition = array(
                'table_name' => "products",
                'table_id' => $id
            );
            $files = Asset::where($condition)->get();
            $fields = Customefield::where($condition)->get();
            $topicimg = Asset::where(['table_name' => "products", 'table_id' => $id, 'type' => "TOPICIMAGE"])->first();
            return view('admin/product/edit', ['result' => $result, 'topicimg' => $topicimg, 'files' => $files, 'fields' => $fields, 'categories' => $categories]);
        } else {
            return view('admin/product/add', $data);
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

    function addformdb(Request $request, $productid = null) {
        $e_messages = [
            'fData.name' => 'Page title is required',
            'fData.slug' => 'Slug is required',
            'fData.details' => 'Details is required',
        ];

        $validator = Validator::make($request->all(), [
                    'fData.name' => 'required',
                    'fData.slug' => 'required',
                    'fData.details' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if ($productid) {
                $fromdata = $request->input('fData');
                Product::where('id', $productid)->update($fromdata);

                if ($productid) {
                    ///....For Field Update...////
                    if ($request->has('FieldE')) {
                        $fileddatas = $request->input('FieldE');
                        foreach ($fileddatas as $key => $data) {
                            $id = $data['id'];
                            Customs_field::where('id', $id)->update($data);
                        }
                    }
                    ///....For Field Update...////
                    /// START FOR CUSTOM FIELD /////
                    if ($request->has('Field')) {
                        $fileddatas = $request->input('Field');
                        Fileupload::customsfiled($fileddatas, $productid, "products");
                    }
                    /// END FOR CUSTOM FIELD /////
                    ///START FOR CUSTOM FILE /////
                    if ($request->has('Filefrm')) {
                        $fileddatas = $request->input('Filefrm');
                        Fileupload::storefile($fileddatas, $request, $productid, "products");
                    }
                    ///END FOR CUSTOM FILE /////
                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $productid, "products");
                    }
                    ///END FOR IMAGE /////
                    /// START FOR TOPICIMAGE ///
                    if ($request->hasFile('topicimage')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('topicimage')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('topicimage')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $caption = $request->input('Topic.caption');
                            $datafile = array(
                                'table_name' => 'products',
                                'table_id' => $productid,
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
                $productid = Product::create($fromdata)->id;
                if ($productid) {
                    /// START FOR CUSTOM FIELD /////
                    if ($request->has('Field')) {
                        $fileddatas = $request->input('Field');
                        Fileupload::customsfiled($fileddatas, $productid, "products");
                    }
                    /// END FOR CUSTOM FIELD /////
                    ///START FOR CUSTOM FILE /////
                    if ($request->has('Filefrm')) {
                        $fileddatas = $request->input('Filefrm');
                        Fileupload::storefile($fileddatas, $request, $productid, "products");
                    }
                    ///END FOR CUSTOM FILE /////
                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $productid, "products");
                    }
                    ///END FOR IMAGE /////
                    /// START FOR TOPICIMAGE ///
                    if ($request->hasFile('topicimage')) {
                        $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                        $currentfileextenion = $request->file('topicimage')->extension();
                        if (in_array($currentfileextenion, $extensions)) {
                            $path = $request->file('topicimage')->store('public/assets');
                            $path = trim(str_replace('public/', '', $path));
                            $caption = $request->input('Topic.caption');
                            $datafile = array(
                                'table_name' => 'products',
                                'table_id' => $productid,
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

    function delete($productid) {
        $conditions = array(
            'table_name' => "products",
            "table_id" => $productid
        );
        $deletedcustomsfile = Customefield::where($conditions)->delete();
        $assets = Asset::where($conditions)->get();
        foreach ($assets as $asset) {
            Storage::delete('public/' . $asset->file_name);
        }
        $deleteasset = Asset::where($conditions)->delete();
        Product::destroy($productid);
    }

    public function slugcreating(Request $request) {
        $pagetitle = $request["name"];

        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = Product::where('slug', $data)->first();

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
                $result = Product::where('slug', $data)->first();
                $i++;
            }
            return $data;
        }
    }

    public function slugchecking(Request $request) {
        $pagetitle = $request["name"];
        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = Product::where('slug', $data)->first();
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
            Storage::delete('public/' . $result->file_name);
            Asset::destroy($id);
        } else {
            Customefield::destroy($id);
        }
    }

}
