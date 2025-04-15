<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\PackageInInvoice;

class InvoiceController extends Controller
{
    public function generateInvoice($invoiceID)
    {
        // if (!Auth::check()) {
        //     abort(401, 'Unauthorized access');
        // }

        $invoice = Invoice::findOrFail($invoiceID);

        // if (Auth::user()->id !== $invoice->company_id) {
        //     abort(403, 'You are not authorized to access this invoice');
        // }

            $data = [
                'invoice' => $invoice,
            ];

        $pdf = Pdf::loadView('customers.invoices.invoice-template', $data);
        return $pdf->stream('invoice.pdf');
    }
}
