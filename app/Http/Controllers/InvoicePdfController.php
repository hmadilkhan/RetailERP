<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function download($id)
    {
        $invoice = Invoice::with(['company', 'lines', 'payments', 'adjustments'])->findOrFail($id);
        
        $pdf = Pdf::loadView('Admin.Billing.invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->invoice_no . '.pdf');
    }

    public function view($id)
    {
        $invoice = Invoice::with(['company', 'lines', 'payments', 'adjustments'])->findOrFail($id);
        
        $pdf = Pdf::loadView('Admin.Billing.invoices.pdf', compact('invoice'));
        
        return $pdf->stream('invoice-' . $invoice->invoice_no . '.pdf');
    }
}
