<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {

    protected $fillable = ['title', 'slug', 'content', 'status', 'details', 'meta_description', 'meta_keywords']; 
}
