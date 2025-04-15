<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\PackageInInvoice;

class InvoiceController extends Controller
{
    public function generateInvoice($invoiceID)
    {
         if (!Auth::check()) {
             abort(401, 'Unauthorized access');
         }

        $invoice = Invoice::findOrFail($invoiceID);

         if (Auth::user()->id !== $invoice->company_id || Auth::user()->is_company !== true) {
             abort(403, 'You are not authorized to access this invoice');
         }

        $packages = Package::whereIn('id', function ($query) use ($invoiceID) {
            $query->select('package_id')
                  ->from('packages_in_invoice')
                  ->where('invoice_id', $invoiceID);
        })->get();

        // Calculate Subtotal (sum of all package prices)
        $subtotal = $packages->sum(function ($package) { 
            return (float)$package->weight_price + (float)$package->delivery_price; 
        });

        // Get discount from invoice
        $discount = (float)$invoice->discount;

        // Calculate discounted subtotal
        $discountedSubtotal = $subtotal - $discount;

        // Calculate VAT (21% on discounted subtotal)
        $vat = $discountedSubtotal * 0.21;

        // Calculate Total (discounted subtotal + VAT)
        $total = $discountedSubtotal + $vat;

        $data = [
            'invoice' => $invoice,
            'company' => $invoice->company,
            'company_address' => $invoice->company->address,
            'packages' => $packages,
            'VAT' => number_format($vat, 2),
            'discount' => number_format($discount, 2),
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($total, 2),
        ];

        $pdf = Pdf::loadView('customers.invoices.invoice-template', $data);
        return $pdf->stream('invoice.pdf');
    }
}
