<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use App\transactions;
use Illuminate\Http\Request;

class BuyerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        // En vez de una colección devuelve un query builder el () en transactions (Eager Loading)
        $products = $buyer->transactions()->with('product')->get()->pluck('product');

        return $this->showAll($products);
    }

}
