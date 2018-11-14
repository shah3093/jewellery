<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller {

    public function home() {
        if (Auth::check()) {
            return redirect()->route('admin.customerorder.index');
        } else {
            return redirect()->route('admin.login');
        }
    }

    public function signinfrom() {
        if (Auth::check()) {
            return redirect()->route('admin.customerorder.index');
        } else {
            return view('admin/login');
        }
    }

    public function signin(Request $request) {

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request['email'];
        $password = $request['password'];

        if (Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            return redirect()->route('admin.customerorder.index');
        } else {
            return redirect()->back()->with(['message' => "Email or password not matched"]);
        }
    }

    public function signout() {
        Auth::logout();
        Auth::guard('customer')->logout();
        return redirect()->route('admin.login');
    }

}
