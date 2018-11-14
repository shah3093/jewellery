<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model {

    //
    protected $fillable = ['table_id', 'table_name', 'caption', 'file_name', 'details', 'type'];

}
