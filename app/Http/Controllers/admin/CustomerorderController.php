<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Asset;
use Illuminate\Support\Facades\Validator;
use App\Customer;

class CustomerorderController extends Controller {

    function index() {
        return view('admin/customerorder/index');
    }

    function listing() {
        $results = Order::with('customer')->orderBy('created_at', 'desc')->get();
        return view('admin/customerorder/listing', ['results' => $results]);
    }

    function getOrderDetails($orderid) {
        $result = $data['result'] = Order::with('customer')->find($orderid);

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

        $data['carts'] = $dCart;

        return view('admin/customerorder/details', $data);
    }

    function updateOrderStatus(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'comment' => 'required',
                    'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all());
        } else {
            $order = Order::find($id);
            $order->comment = $request->input('comment');
            $order->status = $request->input('status');
            $order->save();
            return response()->json(["DONE"]);
        }
    }

}
