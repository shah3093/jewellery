<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shopinformation extends Model {

    protected $fillable = ['about', 'email', 'phone', 'address', 'google_map', 'logo','favicon'];

}
