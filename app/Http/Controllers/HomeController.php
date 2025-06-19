<?php

namespace App\Http\Controllers;

use App\Models\Product; 

class HomeController extends Controller
{
    public function index()
    {

        $recommendedProducts = Product::inRandomOrder() 
                                    ->limit(3) 
                                    ->with(['category', 'manufacturer'])
                                    ->get(); 

        return view('home', compact('recommendedProducts'));
    }
}