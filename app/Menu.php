<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model {

    protected $fillable = ['parent_id', 'menu_url', 'status', 'sortorder', 'name', 'isCustom'];

}
