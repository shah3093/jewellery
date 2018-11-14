<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\ProductCategory;
use App\Customefield;
use App\Asset;
use App\Product;
use App\CustomHelper\Fileupload;

class HomeController extends Controller {

    public function getSliders() {
        return DB::table('assets')->where(['table_name' => 'SLIDER'])->orderBy('created_at', 'desc')->get();
    }

    public function getProductCategoriesAndTopicsimg() {
        $data = array();
        $categories = DB::table('product_categories')->where(['status' => '1'])->get();
        foreach ($categories as $key => $category) {
            $condition = array(
                'table_name' => "product_categories",
                'table_id' => $category->id,
                'type' => "TOPICIMAGE"
            );
            $topicimg = Asset::where($condition)->first();
            $data[$key] = [
                'id' => $category->id,
                'name' => $category->name,
                'details' => $category->details,
                'slug' => $category->slug,
                'caption' => $topicimg->caption,
                'file_name' => $topicimg->file_name
            ];
        }
        return $data;
    }

    public function getProducts($categoryid = null, $limit = null) {
        $data = array();

        if ($categoryid != null) {
            $condition = array(
                'status' => '1',
                'product_category_id' => $categoryid
            );
        } else {
            $condition = array(
                'status' => '1'
            );
        }


        $products = DB::table('products')->where($condition)->orderBy('created_at', 'desc')->limit($limit)->get();
        foreach ($products as $key => $product) {
            $condition = array(
                'table_name' => "products",
                'table_id' => $product->id,
                'type' => "TOPICIMAGE"
            );
            $topicimg = Asset::where($condition)->first();
            $data[$key] = [
                'id' => $product->id,
                'name' => $product->name,
                'details' => $product->details,
                'shortDetails' => $product->shortDetails,
                'slug' => $product->slug,
                'caption' => $topicimg->caption,
                'file_name' => $topicimg->file_name,
                'price' => $product->price
            ];
        }
        return $data;
    }

    //
    public function home() {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        $data['sliders'] = $this->getSliders();
        $data['productcategories'] = $this->getProductCategoriesAndTopicsimg();
        $data['products'] = $this->getProducts(null, 8);
        return view('frontend/index', $data);
    }

    public function productDetails($id) {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        $result = $data['result'] = Product::find($id);
        $condition = array(
            'table_name' => "products",
            'table_id' => $id
        );
        $data['files'] = Asset::where($condition)->get();
        $data['fields'] = Customefield::where($condition)->get();
        $data['relatedProducts'] = $this->getProducts($result->product_category_id, 4);
        $data['topicimg'] = Asset::where(['table_name' => "products", 'table_id' => $id, 'type' => "TOPICIMAGE"])->first();
        return view('frontend/product-details', $data);
    }

    public function categoryDetails($catid) {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        $data['products'] = $this->getProducts($catid);
        return view('frontend/category-details', $data);
    }

    public function cartDetails() {
        $result = $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        if ($result) {
            return view('frontend/cart-details', $data);
        } else {
            return redirect()->back();
        }
    }

    public function content($slug) {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        if ($slug != "contact") {
            $data['result'] = DB::table('pages')->where(['slug' => $slug])->first();
            return view('frontend/page', $data);
        } else {
            $data['result'] = DB::table('shopinformations')->first();
            return view('frontend/contact', $data);
        }
    }

}
