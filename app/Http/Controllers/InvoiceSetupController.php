<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\InvoiceSetup;
use Illuminate\Http\Request;

class InvoiceSetupController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceSetup::with('company');

        if (!empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        if (!empty($request->invoice_type)) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->filled('is_auto_invoice')) {
            $query->where('is_auto_invoice', (int) $request->is_auto_invoice);
        }

        if (!empty($request->search)) {
            $search = trim($request->search);

            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('invoice_prefix', 'like', '%' . $search . '%')
                    ->orWhereHas('company', function ($companyQuery) use ($search) {
                        $companyQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $invoiceSetups = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $companies = Company::select('company_id', 'name')->orderBy('name')->get();

        return view('Admin.InvoiceSetup.index', compact('invoiceSetups', 'companies'));
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
