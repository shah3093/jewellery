<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model {
    //
    protected $fillable = ['parent_id', 'name', 'details', 'status', 'slug', 'meta_description', 'meta_keywords'];

    public function product() {
        return $this->hasMany('App\Product');
    }

}
