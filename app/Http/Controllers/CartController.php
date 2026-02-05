<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Traits\PhpFlasher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    //Add flasher here

    use PhpFlasher;

    /**
     * Display a listing of the resource.
     * This index function shows the items added to cart
     */
    public function index()
    {
        $group_ids = Auth::check() ? Auth::user()->getGroups() : [1];

        $user = Auth::user();

        $cart_data = $user->products()->withPrices()->get(); //gets all the products a user added to cart

        $cart_data->calculateSubtotal();

        return view('pages.default.cartpage', compact('cart_data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            ['quantity' => DB::raw('quantity + ' . $request->quantity), 'updated_at' => now()]
        );

        $this->flashSuccess('Product added to cart');

        return redirect()->route('cart.index')->with('message', 'Product added to the cart');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function addToCartFromStore(Request $request)
    {
        Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->id],
            ['quantity' => DB::raw('quantity + ' . 1), 'updated_at' => now()]
        );
        $this->flashSuccess('Product added to cart');

        return redirect()->route('cart.index')->with('message', 'Product added to the cart');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Cart::destroy($id);

        $this->flashError('Product removed from cart');

        return redirect()->route('cart.index')->with('messasge', "Product removed from cart");
    }
}
