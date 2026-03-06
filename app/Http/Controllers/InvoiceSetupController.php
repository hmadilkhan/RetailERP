<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSetup;
use Illuminate\Http\Request;

class InvoiceSetupController extends Controller
{
    public function index()
    {
        $invoiceSetups = InvoiceSetup::with('company')->get();
        return view('Admin.InvoiceSetup.index', compact('invoiceSetups'));
    }

    public function create()
    {
        return view('Admin.InvoiceSetup.create');
    }

    public function edit($id)
    {
        return view('Admin.InvoiceSetup.edit', compact('id'));
    }
}
