<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\ShippingHelper;
use App\Helpers\StripeCheckout;
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
        // Get groups
        $group_ids = Auth::check() ? Auth::user()->getGroups() : [1];

        // Get user
        $user = Auth::user();

        // Create variables
        $shipping_helper = new ShippingHelper();
        $stripe_checkout = new StripeCheckout();
        $order = new Order();
        $insert_data = [];
        $completed = false;

        // Get products
        $cart_data = $user->products()->withPrices()->get();

        // Check if cart is empty
        if ($cart_data->isEmpty()) {
            return redirect()->route('cart.index')->with('message', 'your cart is empty');
        }

        // Get Subtotal
        $cart_data->calculateSubtotal();

        // Determine payment
        switch ($payment) {
            case 'stripe':
                # code...
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

        // Create order details

        // Create order details

        // Redirect

        return true;
    }
}
