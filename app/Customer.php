<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class Customer extends Model implements Authenticatable {

    //
    use \Illuminate\Auth\Authenticatable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'address'];

    public function orders() {
        return $this->hasMany('App\Order');
    }

}
