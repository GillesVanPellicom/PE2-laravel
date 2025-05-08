<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\PackageInInvoice;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function myinvoices(Request $request)
    {
        if (!Auth::check() || Auth::user()->isCompany !== 1) {
            abort(401, 'Unauthorized access');
        }

        $query = Invoice::with(['company'])
        ->where('company_id', Auth::user()->id);

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;

            $numericId = null;
            if (preg_match('/INV-(\d+)/', $searchTerm, $matches)) {
                $numericId = $matches[1];
            }

            $query->where(function($q) use ($searchTerm, $numericId) {
                $q->where('id', 'LIKE', "%{$searchTerm}%")
                ->orWhere('id', $numericId)
                ->orWhereHas('company', function($q) use ($searchTerm) {
                    $q->where('company_name', 'LIKE', "%{$searchTerm}%");
                });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== 'All Invoices') {
            switch ($request->status) {
                case 'Paid':
                    $query->where('is_paid', true);
                    break;
                case 'Pending':
                    $query->where('is_paid', false)
                          ->where('expiry_date', '>', now());
                    break;
                case 'Overdue':
                    $query->where('is_paid', false)
                          ->where('expiry_date', '<', now());
                    break;
            }
        }

        $invoices = $query->paginate(10)->withQueryString();

        // Calculate packages and amount for each invoice
        foreach ($invoices as $invoice) {
            // Get package count
            $invoice->package_count = PackageInInvoice::where('invoice_id', $invoice->id)->count();

            // Get all packages in this invoice
            $packages = Package::whereIn('id', function ($query) use ($invoice) {
                $query->select('package_id')
                      ->from('packages_in_invoice')
                      ->where('invoice_id', $invoice->id);
            })->get();

            // Calculate Subtotal (sum of all package prices)
            $subtotal = $packages->sum(function ($package) {
                return (float)$package->weight_price + (float)$package->delivery_price;
            });

            // Get discount from invoice
            $discount = (float)$invoice->discount;

            // Calculate discounted subtotal (ensure it doesn't go below 0)
            $discountedSubtotal = max(0, $subtotal - $discount);

            // Calculate VAT (21%) on the discounted subtotal
            $vat = $discountedSubtotal * 0.21;

            // Calculate Total (discounted subtotal + VAT)
            $total = $discountedSubtotal + $vat;

            // Add all values to the invoice object
            $invoice->subtotal = $subtotal;
            $invoice->discounted_subtotal = $discountedSubtotal;
            $invoice->total_amount = $total;

            // Determine status and color  $discount = (float)$invoice->discount;

            // Calculate discounted subtotal (ensure it doesn't go below 0)
            $discountedSubtotal = max(0, $subtotal - $discount);

            // Calculate VAT (21%) on the discounted subtotal
            $vat = $discountedSubtotal * 0.21;

            // Calculate Total (discounted subtotal + VAT)
            $total = $discountedSubtotal + $vat;

            // Add all values to the invoice object
            $invoice->subtotal = $subtotal;
            $invoice->discounted_subtotal = $discountedSubtotal;
            $invoice->total_amount = $total;

            // Determine status and color
            if ($invoice->is_paid) {
                $invoice->status = 'Paid ('.$invoice->paid_at.')';
                $invoice->status_color = 'green';
            } else {
                if ($invoice->expiry_date < now()) {
                    $invoice->status = 'Overdue ('.$invoice->expiry_date.')';
                    $invoice->status_color = 'red';
                } else {
                    $invoice->status = 'Pending';
                    $invoice->status_color = 'orange';
                }
            }
        }

        // Calculate total outstanding amount (sum of unpaid invoices)
        $totalOutstanding = $invoices->where('is_paid', false)
            ->sum('total_amount');

        // Calculate total paid this month
        $totalPaidThisMonth = $invoices->where('is_paid', true)
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('total_amount');

        $totalpackages = PackageInInvoice::whereIn('invoice_id', function($query) {
            $query->select('id')
                ->from('invoices')
                ->where('company_id', Auth::user()->id);
        })->count();

        return view('customers.invoices.invoice-overview', [
            'invoices' => $invoices,
            'totalpackages' => $totalpackages,
            'totalOutstanding' => $totalOutstanding,
            'totalPaidThisMonth' => $totalPaidThisMonth,
            'selectedStatus' => $request->status ?? 'All Invoices',
            'searchTerm' => $request->search ?? ''
        ]);
    }

    public function generateInvoice($invoiceID)
    {
         if (!Auth::check()) {
             abort(401, 'Unauthorized access');
         }

        $invoice = Invoice::findOrFail($invoiceID);

         if (Auth::user()->id !== $invoice->company_id || Auth::user()->isCompany !== 1) {
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
        $discountedSubtotal = max(0, $subtotal - $discount);

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
    public function manageInvoices() {
        $invoices = Invoice::all();
        return view('employees.manage_invoices',compact('invoices'));

    }

    /**
 * Display unpaid invoices
 *
 * @return \Illuminate\View\View
 */
public function getUnpaidInvoices(Request $request)
{

    try {
        $SelectedInvoice = request('invoice');
        $payments = [];
        if (!empty($SelectedInvoice)) {
            $invoice = Invoice::findOrFail($SelectedInvoice);
            if (!$invoice->is_paid) {
                $payments = InvoicePayment::where('reference', $invoice->reference)
                    ->with('invoice')  // Eager load the invoice relationship
                    ->orderBy('created_at', 'desc')  // Order by created date
                    ->paginate(5);  // Add pagination, 10 items per page
            }
        }

        $unpaidInvoices = Invoice::where('is_paid', false)
            ->with('company')  // Eager load the company relationship
            ->orderBy('expiry_date', 'asc')  // Order by expiry date
            ->paginate(5);    // Add pagination, 10 items per page


                 // Calculate packages and amount for each invoice
        foreach ($unpaidInvoices as $invoice) {



            // Get all packages in this invoice
            $packages = Package::whereIn('id', function ($query) use ($invoice) {
                $query->select('package_id')
                      ->from('packages_in_invoice')
                      ->where('invoice_id', $invoice->id);
            })->get();

            // Calculate Subtotal (sum of all package prices)
            $subtotal = $packages->sum(function ($package) {
                return (float)$package->weight_price + (float)$package->delivery_price;
            });

            // Get discount from invoice
            $discount = (float)$invoice->discount;

            // Calculate discounted subtotal (ensure it doesn't go below 0)
            $discountedSubtotal = max(0, $subtotal - $discount);

            // Calculate VAT (21%) on the discounted subtotal
            $vat = $discountedSubtotal * 0.21;

            // Calculate Total (discounted subtotal + VAT)
            $total = $discountedSubtotal + $vat;

            // Add all values to the invoice object
            $invoice->subtotal = $subtotal;
            $invoice->discounted_subtotal = $discountedSubtotal;
            $invoice->total_amount = $total;


        }

        return view('invoices.invoice-payment-overview', [
            'invoices' => $unpaidInvoices,
            'payments' => $payments,
        ]);

    } catch (\Exception $e) {
        return back()->with('error', 'Failed to retrieve unpaid invoices: ' . $e->getMessage());
    }
}

/**
     * Mark invoice as paid
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsPaid(Request $request)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($request->invoice);

            if ($invoice->is_paid) {
                return redirect()
                    ->back()
                    ->with('error', "Invoice #{$invoice->id} is already marked as paid.");
            }

            $invoice->update([
                'is_paid' => true,
                'paid_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', "Invoice #{$invoice->id} has been marked as paid successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', "Failed to mark invoice as paid: {$e->getMessage()}");
        }
    }
}


