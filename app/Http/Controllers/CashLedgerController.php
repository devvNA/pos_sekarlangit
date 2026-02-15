<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CashLedgerController extends Controller
{
    public function index(): View
    {
        return view('cash.index');
    }
}
