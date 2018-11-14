<?php

namespace App\Http\Controllers\admin;

use App\Asset;
use App\Page;
use App\CustomHelper\Fileupload;
use App\Customefield;
use App\Product;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller {

    function index() {
        return view('admin/page/index');
    }

    function listing() {
        $results = DB::table('pages')->orderBy('created_at', 'desc')->get();
        return view('admin/page/listing', ['results' => $results]);
    }

    function formview($id = null) {
        if ($id) {
            $result = Page::find($id);
            $condition = array(
                'table_name' => "pages",
                'table_id' => $id
            );
            $files = Asset::where($condition)->get();
            $fields = Customefield::where($condition)->get();
            $topicimg = Asset::where(['table_name' => "pages", 'table_id' => $id, 'type' => "TOPICIMAGE"])->first();
            return view('admin/page/edit', ['result' => $result, 'topicimg' => $topicimg, 'files' => $files, 'fields' => $fields]);
        } else {
            return view('admin/page/add');
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

    function addformdb(Request $request, $pageid = null) {
        $e_messages = [
            'fData.title' => 'Page title is required',
            'fData.slug' => 'Slug is required',
            'fData.content' => 'Content is required',
        ];

        $validator = Validator::make($request->all(), [
                    'fData.title' => 'required',
                    'fData.slug' => 'required',
                    'fData.content' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if ($pageid) {
                $fromdata = $request->input('fData');
                Page::where('id', $pageid)->update($fromdata);

                if ($pageid) {
                    ///....For Field Update...////
                    if ($request->has('FieldE')) {
                        $fileddatas = $request->input('FieldE');
                        foreach ($fileddatas as $key => $data) {
                            $id = $data['id'];
                            Customefield::where('id', $id)->update($data);
                        }
                    }
                    ///....For Field Update...////
                    /// START FOR CUSTOM FIELD /////
                    if ($request->has('Field')) {
                        $fileddatas = $request->input('Field');
                        Fileupload::customsfiled($fileddatas, $pageid, "pages");
                    }
                    /// END FOR CUSTOM FIELD /////
                    ///START FOR CUSTOM FILE /////
                    if ($request->has('Filefrm')) {
                        $fileddatas = $request->input('Filefrm');
                        Fileupload::storefile($fileddatas, $request, $pageid, "pages");
                    }
                    ///END FOR CUSTOM FILE /////
                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $pageid, "pages");
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
                                'table_name' => 'pages',
                                'table_id' => $pageid,
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
                $pageid = Page::create($fromdata)->id;
                if ($pageid) {
                    /// START FOR CUSTOM FIELD /////
                    if ($request->has('Field')) {
                        $fileddatas = $request->input('Field');
                        Fileupload::customsfiled($fileddatas, $pageid, "pages");
                    }
                    /// END FOR CUSTOM FIELD /////
                    ///START FOR CUSTOM FILE /////
                    if ($request->has('Filefrm')) {
                        $fileddatas = $request->input('Filefrm');
                        Fileupload::storefile($fileddatas, $request, $pageid, "pages");
                    }
                    ///END FOR CUSTOM FILE /////
                    ///  ///START FOR IMAGE/////
                    if ($request->has('Image')) {
                        $fileddatas = $request->input('Image');
                        Fileupload::storeimage($fileddatas, $request, $pageid, "pages");
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
                                'table_name' => 'pages',
                                'table_id' => $pageid,
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

    function delete($pageid) {
        $conditions = array(
            'table_name' => "pages",
            "table_id" => $pageid
        );
        $deletedcustomsfile = Customefield::where($conditions)->delete();
        $assets = Asset::where($conditions)->get();
        foreach ($assets as $asset) {
            Storage::delete('public/' . $asset->file_name);
        }
        $deleteasset = Asset::where($conditions)->delete();
        Page::destroy($pageid);
    }

    public function slugcreating(Request $request) {
        $pagetitle = $request["title"];

        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = Page::where('slug', $data)->first();

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
                $result = Page::where('slug', $data)->first();
                $i++;
            }
            return $data;
        }
    }

    public function slugchecking(Request $request) {
        $pagetitle = $request["title"];
        $tmp = explode(" ", $pagetitle);
        $data = implode("-", $tmp);
        $result = Page::where('slug', $data)->first();
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
