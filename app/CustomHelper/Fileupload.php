<?php

namespace App\CustomHelper;

use App\Asset;
use App\Customefield;
use Image;
use Cart;
use App\Product;
use Illuminate\Support\Facades\DB;

class Fileupload {

    public static function storeimage($fileddatas, $request, $tableid, $tablename) {
        foreach ($fileddatas as $key => $fileddata) {
            if ($request->hasFile('img_' . $key)) {
                $extensions = ['gif', 'jpg', 'png', 'jpeg'];
                $currentfileextenion = $request->file('img_' . $key)->extension();
                if (in_array($currentfileextenion, $extensions)) {
                    $path = $request->file('img_' . $key)->store('public/images');
                    $path = trim(str_replace('public/', '', $path));
                    $datafile = array(
                        'table_name' => $tablename,
                        'table_id' => $tableid,
                        'file_name' => $path,
                        'caption' => $fileddata['caption'],
                        'type' => "IMAGE"
                    );
                    $assetsfiled = Asset::create($datafile);
                }
            }
        }
    }

    public static function storefile($fileddatas, $request, $tableid, $tablename) {
        foreach ($fileddatas as $key => $fileddata) {
            if ($request->hasFile('file_' . $key)) {
                $path = $request->file('file_' . $key)->store('public/assets');
                $path = trim(str_replace('public/', '', $path));
                $datafile = array(
                    'table_name' => $tablename,
                    'table_id' => $tableid,
                    'file_name' => $path,
                    'caption' => $fileddata['caption'],
                    'type' => "FILE"
                );
                $assetsfiled = Asset::create($datafile);
            }
        }
    }

    public static function customsfiled($fileddatas, $tableid, $tablename) {
        foreach ($fileddatas as $fileddata) {
            $fileddata['table_name'] = $tablename;
            $fileddata['table_id'] = $tableid;
            $customsfiled = Customefield::create($fileddata);
        }
    }

    public static function imageResize($imageName, $height, $width) {
        $thumbnailpath = public_path('storage/' . $imageName);
        $img = Image::make($thumbnailpath)->resize($height, $width);

        $img->save($thumbnailpath);
    }

    public static function cartProductList() {
        $data = array();
        foreach (Cart::content() as $key => $row) {

            $condition = array(
                'table_name' => "products",
                'table_id' => $row->id,
                'type' => "TOPICIMAGE"
            );
            $topicimg = Asset::where($condition)->first();

            $data[$key] = array(
                'file_name' => $topicimg->file_name,
                'productid' => $row->id,
                'productName' => $row->name,
                'price' => $row->price,
                'quantity' => $row->qty,
                'rowid' => $key
            );
        }
        return $data;
    }

    public static function categoryList() {
        $categories = DB::table('product_categories')->where(['status' => '1'])->get();
        return $categories;
    }
    
     public static function shopinformations() {
         $result = DB::table('shopinformations')->first();
        return $result;
    }

}
