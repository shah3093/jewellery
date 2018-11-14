<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CustomHelper\Fileupload;
use App\Customer;
use Cart;
use App\Order;
use App\Asset;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller {

    public function cutomerRegistrationfrm() {

        $data['categoriesList'] = Fileupload::categoryList();   
        $data['shopInformations'] = Fileupload::shopinformations();     
        $data['cartDetails'] = Fileupload::cartProductList();

        if (Auth::guard('customer')->check()) {
            return redirect()->back();
        } else {
            return view('frontend/customer/customer-regestration', $data);
        }
    }

    public function addCustomer(Request $request, $customerid = null) {
        $e_messages = [
            'Customer.name' => 'Name is required',
            'Customer.email' => 'Email is required',
            'Customer.password' => 'Password is required',
            'Customer.phone' => 'Phone is required'
        ];

        $validator = Validator::make($request->all(), [
                    'Customer.name' => 'required',
                    'Customer.email' => 'required | email | unique:customers,email' . $customerid,
                    'Customer.password' => 'required',
                    'Customer.phone' => 'required'
                        ], $e_messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            $fromdata = $request->input('Customer');
            $password = $fromdata['password'];
            $fromdata['password'] = bcrypt($fromdata['password']);
            $customerid = Customer::create($fromdata)->id;
            if ($customerid) {
                if (Auth::guard('customer')->attempt(['email' => $fromdata['email'], 'password' => $password])) {
                    return response()->json(["DONE"]);
                } else {
                    return redirect()->back()->with(['message' => "Email or password not matched"]);
                }
            } else {
                return response()->json(["Someting went wrong"]);
            }
        }
    }

    public function profile() {
        $data['profile_menu_status'] = true;
        $data['categoriesList'] = Fileupload::categoryList();    
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        return view('frontend/customer/customer-profile', $data);
    }

    public function changepasswrodform() {
        $data['changepass_menu_status'] = true;
        $data['categoriesList'] = Fileupload::categoryList();      
        $data['shopInformations'] = Fileupload::shopinformations();  
        $data['cartDetails'] = Fileupload::cartProductList();
        return view('frontend/customer/changepasssword', $data);
    }

    public function signinfrom() {
        return view('frontend/customer/signin');
    }

    public function signin(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request['email'];
        $password = $request['password'];

        if (Auth::guard('customer')->attempt(['email' => $email, 'password' => $password])) {
            return response()->json(["DONE"]);
        } else {
            return response()->json(["Email or password not matched"]);
        }
    }

    public function changepassword(Request $request, $customerid) {
        $validator = Validator::make($request->all(), [
                    'currentPassword' => 'required',
                    'newPassword' => 'required',
                    'retypePassword' => 'required|same:newPassword'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            if (!(Hash::check($request->get('currentPassword'), Auth::user()->password))) {
                return response()->json(["Current password not matched"]);
            }
            $user = Customer::find($customerid);
            $user->password = bcrypt($request->get('newPassword'));
            $user->save();
        }
    }

    public function signout() {
        Auth::guard('customer')->logout();
        Cart::destroy();
        return redirect()->route('home');
    }

    public function updateprofile(Request $request, $customerid) {
        $fromdata = $request->input('Customer');
        Customer::where('id', $customerid)->update($fromdata);
        return response()->json(["DONE"]);
    }

    public function changeshippingaddress() {
        return view('frontend/changeshippingaddress');
    }

    public function getOrdersList() {
        $ordelist = array();
        $data['orders_menu_status'] = true;
        $data['categoriesList'] = Fileupload::categoryList();     
        $data['shopInformations'] = Fileupload::shopinformations();  
        $data['cartDetails'] = Fileupload::cartProductList();


        $results = Auth::user()->orders()->orderBy('created_at', 'desc')->get();

        foreach ($results as $key => $result) {
            $total = 0;
            $carts = unserialize($result->cart);
            foreach ($carts as $cart) {
                $total = $total + ($cart->price * $cart->qty);
            }

            $ordelist[$key] = array(
                'id' => $result->id,
                'orderid' => $result->orderid,
                'status' => $result->status,
                'totalprice' => $total,
                'orderDate' => date("d-m-Y", strtotime($result->created_at))
            );
        }

        $data['results'] = $ordelist;

        return view('frontend/customer/order', $data);
    }

    public function getOrdersDetails($id) {
        $dCart = array();
        $data['categoriesList'] = Fileupload::categoryList();    
        $data['shopInformations'] = Fileupload::shopinformations();    
        $data['cartDetails'] = Fileupload::cartProductList();
        $result = Auth::user()->orders()->orderBy('created_at', 'desc')->find($id);

        $carts = unserialize($result->cart);

        foreach ($carts as $key => $cart) {

            $condition = array(
                'table_name' => "products",
                'table_id' => $cart->id,
                'type' => "TOPICIMAGE"
            );
            $image = Asset::where($condition)->first();

            $dCart[$key]['productname'] = $cart->name;
            $dCart[$key]['productid'] = $cart->id;
            $dCart[$key]['productimg'] = $image->file_name;
            $dCart[$key]['price'] = $cart->price;
            $dCart[$key]['quantity'] = $cart->qty;
        }



        $data['orderid'] = $result->orderid;
        $data['comment'] = $result->comment;
        $data['status'] = $result->status;
        $data['orderDate'] = date("d-m-Y", strtotime($result->created_at));
        $data['customerName'] = $result->customer->name;
        $data['customerEmail'] = $result->customer->email;
        $data['customerPhone'] = $result->customer->phone;
        $data['carts'] = $dCart;
        $data['shippingAddress'] = $result->shippingaddress;

        return view('frontend/customer/oder-details', $data);
    }

}
