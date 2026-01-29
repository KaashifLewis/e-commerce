<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\PhpFlasher;
use Illuminate\Support\Facades\Auth;

class DetailController extends Controller
{
    use PhpFlasher;

    public function index($id)
    {
        //checks to see if user is logged in and what group the user belong to.
        $group_ids = Auth::check() ? Auth::user()->getGroups() : [1];

        //fetch data from a single product from the product table(database).
        $data = Product::singleProduct($id)->withPrices()->get()->first();

        //pass information to the page called productspage.
        return view('pages.default.detailspage', compact('data'));
    }
}
