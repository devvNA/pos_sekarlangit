<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('suppliers.index');
    }
}
