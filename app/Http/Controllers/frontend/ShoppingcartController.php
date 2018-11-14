<?php

namespace App\Http\Controllers\frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cart;
use App\Product;
use App\Asset;
use App\Customer;
use App\Order;
use App\CustomHelper\Fileupload;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Charge;

class ShoppingcartController extends Controller {

    public function addProduct(Request $request) {
        $productid = $request->input('productid');
        $productqty = $request->input('productqty');

        $product = Product::find($productid);

        Cart::add($product->id, $product->name, $productqty, $product->price);

        $data['cartDetails'] = Fileupload::cartProductList();

        return view('frontend/header-cart-details', $data);
    }

    public function removeProduct(Request $request) {
        $rowid = $request->input('rowid');
        Cart::remove($rowid);

        $data['cartDetails'] = Fileupload::cartProductList();

        return view('frontend/header-cart-details', $data);
    }

    public function updateProduct(Request $request) {
        $rowid = $request->input('rowid');
        $qty = $request->input('qty');

        if ($qty > 0) {
            Cart::update($rowid, $qty);
        } else {
            Cart::remove($rowid);
        }


        $data['cartDetails'] = Fileupload::cartProductList();

        return view('frontend/header-cart-details', $data);
    }

    public function processtocheckout() {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        return view('frontend/processtocheckout', $data);
    }

    public function proceedtopayment(Request $request) {
        $referencenumber = $request->input('referencenumber');
        $shippingaddress = $request->input('shippingaddress');

        session([
            'referencenumber' => $referencenumber,
            'shippingaddress' => $shippingaddress
        ]);
        $nexttoload = route("cart.paymentmethodprocess");
        return response()->json([$nexttoload]);
    }

    public function paymentmethodprocess() {
        $data['categoriesList'] = Fileupload::categoryList();
        $data['shopInformations'] = Fileupload::shopinformations();
        $data['cartDetails'] = Fileupload::cartProductList();
        return view('frontend/paymentprocess', $data);
    }

    public function paymentconfirmation(Request $request, $type) {

        if ($type == "card") {
            $stripeToken = $request->input('stripeToken');
            Stripe::setApiKey("sk_test_lL8FKEhVSNrMGVSvG84cznnc");

            $total = Cart::total();
            $total = (int) str_replace(",", "", $total);


            try {
                $charge = Charge::create([
                            'amount' => $total * 100,
                            'currency' => 'BDT',
                            'description' => 'Example charge',
                            'source' => $stripeToken,
                ]);


                $order = new Order();
                $order->cart = serialize(Cart::content());
                $order->shippingaddress = session('shippingaddress');
                $order->orderid = session('referencenumber');
                $order->paymentType = "Online payment";
                $order->payment_id = $charge->id;
                $order->status = "PENDING";

                Auth::user()->orders()->save($order);

                $nexttoload = route("customer.ordersList");
                Cart::destroy();
                return response()->json([$nexttoload]);
            } catch (\Exception $ex) {
                return response()->json([$ex->getMessage()]);
            }
        } else {
            $order = new Order();
            $order->cart = serialize(Cart::content());
            $order->shippingaddress = session('shippingaddress');
            $order->orderid = session('referencenumber');
            $order->paymentType = "Cash on delivery";
            $order->status = "PENDING";

            Auth::user()->orders()->save($order);

            $nexttoload = route("customer.ordersList");
            Cart::destroy();
            return response()->json([$nexttoload]);
        }
    }

}
