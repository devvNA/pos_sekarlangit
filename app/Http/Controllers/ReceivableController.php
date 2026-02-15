<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(): View
    {
        return view('receivables.index');
    }
}
