<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    //
    protected $fillable = ['product_category_id','slug','name','status','details','price','quantity','meta_description','meta_keywords'];
    
    public function productcategory() {
        return $this->belongsTo('App\ProductCategory','product_category_id');
    }

}
