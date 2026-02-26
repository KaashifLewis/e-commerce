<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\ShippingHelper;
use App\Helpers\StripeCheckout;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Auth;


class CheckoutPaymentController extends Controller
{


    /**
     * Undocumented function
     *
     * @param [type] $payment
     * @return void
     */
    public function index($payment)
    {
        // This checks if the user is logged in and determine what group this user belongs to.
        $group_ids = Auth::check() ? Auth::user()->getGroups() : [1];

        // This function gets the user who is logged in.
        $user = Auth::user();

        // Create variables
        $shipping_helper = new ShippingHelper();
        $stripe_checkout = new StripeCheckout();
        $order = new Order();
        $insert_data = [];
        $completed = false;

        // This function gets all the products the user added to the cart.
        $cart_data = $user->products()->withPrices()->get();

        // This function checks to see if user's cart is empty
        if ($cart_data->isEmpty()) {
            // displays a message to tell them its empty and redirect them back to cart page
            return redirect()->route('cart.index')->with('message', 'Your cart is empty');
        }

        // The function displays the orginal cost of the item before deductions.
        $cart_data->calculateSubtotal();

        // Determine payment
        switch ($payment) {
            case 'stripe':
                # code...


                $stripe_checkout->startCheckoutSession();
                $stripe_checkout->addEmail($user->email);
                $stripe_checkout->addProducts($cart_data);
                $stripe_checkout->addPointsCoupon();
                $stripe_checkout->enablePromoCodes();
                $shipping_data = $shipping_helper->getGroupShippingOptions();
                $stripe_checkout->addShippingOptions($shipping_data);
                $stripe_checkout->createSession();
                $insert_data = $stripe_checkout->getOrderCreateData();
                $completed = true;

                break;

            default:
                $insert_data = [
                    'payment_provider' => 'testing',
                    'payment_id' => 'testing',
                ];
                $completed = true;
                break;
        }

        // Validate
        if (!$completed || empty($insert_data)) {
            dd('Payment is incomplete or checkout is missing');
        }
        // Inserting values into the orders table
        $order->user_id = $user->id;
        $order->order_no = '123';
        $order->subtotal = $cart_data->getSubtotal();
        $order->total = $cart_data->getTotal();
        $order->payment_provider = $insert_data['payment_provider'];
        $order->payment_id = $insert_data['payment_id'];
        $order->shipping_id = 1;
        $order->shipping_address_id = 1;
        $order->billing_address_id = 1;
        $order->payment_status = 'unpaid';
        $order->save();


        // This is a loop that goes through each item in the cart
        $records = [];

        // This array
        foreach ($cart_data as $data) {
            array_push(
                $records,
                new OrderProduct([
                    'product_id' => $data->id,
                    'user_id' => $user->id,
                    'price' => $data->getPrice(),
                    'quantity' => $data->pivot->quantity
                ])
            );
        }

        // This line saves all the information and insert it into the order_products table
        $order->order_products()->saveMany($records);

        // Redirect
        if ($payment == 'stripe') {
            return redirect($stripe_checkout->getUrl());
        }

        dd('Payment was successful during testing');
    }
}
