<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxiPackage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $packages = TaxiPackage::where('is_active', true)->get();
        return view('home', compact('packages'));
    }
}
