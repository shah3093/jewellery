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

class SliderController extends Controller {

    function index() {
        return view('admin/slider/index');
    }

    function listing() {
        $results = DB::table('assets')->where(['table_name' => 'SLIDER'])->orderBy('created_at', 'desc')->get();
        return view('admin/slider/listing', ['results' => $results]);
    }

    function formview($id = null) {
        if ($id) {
            $data['result'] = Asset::find($id);
            return view('admin/slider/edit', $data);
        } else {
            return view('admin/slider/add');
        }
    }

    function addformdb(Request $request, $sliderid = null) {
        $e_messages = [
            'fData.caption' => 'Caption is required'
        ];

        $validator = Validator::make($request->all(), [
                    'fData.caption' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if ($sliderid) {

                $fromdata = $request->input('fData');
                Asset::where('id', $sliderid)->update($fromdata);

                if ($request->hasFile('img')) {
                    $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                    $currentfileextenion = $request->file('img')->extension();
                    if (in_array($currentfileextenion, $extensions)) {
                        $path = $request->file('img')->store('public/images');
                        $path = trim(str_replace('public/', '', $path));
                        $datafile = array(
                            'file_name' => $path
                        );
                        Asset::where('id', $sliderid)->update($datafile);
                        return response()->json(["DONE"]);
                    }
                }
                return response()->json(["DONE"]);
            } else {
                $fromdata = $request->input('fData');
                if ($request->hasFile('img')) {
                    $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                    $currentfileextenion = $request->file('img')->extension();
                    if (in_array($currentfileextenion, $extensions)) {
                        $path = $request->file('img')->store('public/images');
                        $path = trim(str_replace('public/', '', $path));
                        $datafile = array(
                            'table_name' => 'SLIDER',
                            'file_name' => $path,
                            'caption' => $fromdata['caption'],
                            'details' => $fromdata['details']
                        );
                        $assetsfiled = Asset::create($datafile);
                        return response()->json(["DONE"]);
                    }
                } else {
                    return response()->json(["Someting went wrong"]);
                }
            }
        }
    }

    public function deletefile($id) {
        $result = Asset::find($id);
        if ($result) {
            Storage::delete('public/' . $result->file_name);
            return response()->json(["Someting went wrong"]);
        }
    }

    function delete($id) {
        $result = Asset::find($id);
        if ($result) {
            Storage::delete('public/' . $result->file_name);
        }
        Asset::destroy($id);
    }

}
