<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\PhpFlasher;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    use PhpFlasher;

    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        //check if the user is logged in and which groups the user belongs to.
        $group_ids = Auth::check() ? Auth::user()->getGroups() : [1];

        //gets data from products table (database).
        $product_data = Product::withPrices()->get();

        //pass information to the page called productspage.
        return view('pages.default.productspage', compact('product_data'));
    }
}
