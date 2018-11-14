<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\ProductCategory;
use App\Customefield;
use App\Shopinformation;
use App\Asset;
use App\CustomHelper\Fileupload;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ShopinformationController extends Controller {

    function index() {
        return view('admin/shopinformation/index');
    }

    function listing() {
        $data['result'] = DB::table('shopinformations')->first();
        return view('admin/shopinformation/listing', $data);
    }

    function formview() {
        $data['result'] = DB::table('shopinformations')->first();
        return view('admin/shopinformation/edit', $data);
    }

    function updateInformation(Request $request, $id) {


        if ($request->hasFile('favicon')) {
            $extensions = ['gif', 'jpg', 'png', 'jpeg', 'ico'];
            $currentfileextenion = $request->file('favicon')->extension();
            if (in_array($currentfileextenion, $extensions)) {
                $path = $request->file('favicon')->store('public/assets');
                $path = trim(str_replace('public/', '', $path));
                ///Image resize
                $imageName = trim(str_replace('public/assets/', '', $path));
                Fileupload::imageResize($imageName, 50, 50);
                $data = [
                    'favicon' => $path
                ];
                $_POST['fData']['favicon'] = $path;
//                array_push($fromdata,$data);
            }
        }

        if ($request->hasFile('logo')) {
            $extensions = ['gif', 'jpg', 'png', 'jpeg'];
            $currentfileextenion = $request->file('logo')->extension();
            if (in_array($currentfileextenion, $extensions)) {
                $path = $request->file('logo')->store('public/assets');
                $path = trim(str_replace('public/', '', $path));
                ///Image resize
                $imageName = trim(str_replace('public/assets/', '', $path));
                Fileupload::imageResize($imageName, 180, 50);

                $data = [
                    'logo' => $path
                ];

                $_POST['fData']['logo'] = $path;
//                array_push($fromdata,$data);
            }
        }

        $fromdata = $_POST['fData'];

        Shopinformation::where('id', $id)->update($fromdata);
        return response()->json(["DONE"]);
    }

    public function deletefile($type) {
        $info = Shopinformation::find(1);
        if ($type == "logo") {
            $info->logo = null;
        } else if ($type == "favicon") {
            $info->favicon = null;
        }
        $info->save();
    }

}
