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

        $invoices = $query->paginate(100)->withQueryString();

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
            if (!Auth::user()->hasPermissionTo("*"))
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
    public function manageInvoices(Request $request) {
        $query = Invoice::query();

        // Apply filter if status is set in the GET request
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_paid', false)->where('expiry_date', '>=', now());
            } elseif ($request->status === 'overdue') {
                $query->where('is_paid', false)->where('expiry_date', '<', now());
            }
        }

        $invoices = $query->paginate(100)->appends($request->query());

        // Get all payments for these invoices
        $payments = InvoicePayment::whereIn('reference', $invoices->pluck('reference'))->get();

        // Associate payments with their invoices
        foreach ($invoices as $invoice) {
            $invoice->payment_amount = $payments->where('reference', $invoice->reference)->sum('amount');
        }
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
        // Get selected invoices as array of IDs (from GET or POST)
        $selectedInvoices = $request->input('invoices', []);
        if (!is_array($selectedInvoices)) {
            $selectedInvoices = [$selectedInvoices];
        }

        // Get unpaid invoices for display
        $unpaidInvoices = Invoice::where('is_paid', false)
            ->with('company')
            ->orderBy('expiry_date', 'asc')
            ->paginate(5);

        // Calculate packages and amount for each invoice
        foreach ($unpaidInvoices as $invoice) {
            $packages = Package::whereIn('id', function ($query) use ($invoice) {
                $query->select('package_id')
                      ->from('packages_in_invoice')
                      ->where('invoice_id', $invoice->id);
            })->get();

            $subtotal = $packages->sum(function ($package) {
                return (float)$package->weight_price + (float)$package->delivery_price;
            });
            $discount = (float)$invoice->discount;
            $discountedSubtotal = max(0, $subtotal - $discount);
            $vat = $discountedSubtotal * 0.21;
            $total = $discountedSubtotal + $vat;

            $invoice->subtotal = $subtotal;
            $invoice->discounted_subtotal = $discountedSubtotal;
            $invoice->total_amount = $total;
        }

        // Get all payments for selected invoices
        $payments = collect();
        if (!empty($selectedInvoices)) {
            // Get all references for selected invoices
            $selectedInvoiceModels = Invoice::whereIn('id', $selectedInvoices)->get();
            $references = $selectedInvoiceModels->pluck('reference')->toArray();

            $payments = \App\Models\InvoicePayment::whereIn('reference', $references)
                ->with(['invoice'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Attach packages to each payment (assuming you have a relation or you can fetch by reference)
            foreach ($payments as $payment) {
                // Find the invoice for this payment
                $inv = $selectedInvoiceModels->firstWhere('reference', $payment->reference);
                if ($inv) {
                    $payment->packages = Package::whereIn('id', function ($query) use ($inv) {
                        $query->select('package_id')
                              ->from('packages_in_invoice')
                              ->where('invoice_id', $inv->id);
                    })->get();
                } else {
                    $payment->packages = collect();
                }
            }
        }

        return view('invoices.invoice-payment-overview', [
            'invoices' => $unpaidInvoices,
            'payments' => $payments,
            'selectedInvoices' => $selectedInvoices,
        ]);
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to retrieve unpaid invoices: ' . $e->getMessage());
    }
}


public function markAsPaid(Request $request)
{
    try {
        DB::beginTransaction();

        $invoiceIds = $request->input('invoices', []);
        if (!is_array($invoiceIds)) {
            $invoiceIds = [$invoiceIds];
        }

        $marked = [];
        foreach ($invoiceIds as $invoiceId) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && !$invoice->is_paid) {
                $invoice->update([
                    'is_paid' => true,
                    'paid_at' => now()
                ]);
                $marked[] = $invoice->id;
            }
        }

        DB::commit();

        // Remove marked invoices from selection for redirect
        $remaining = array_diff($invoiceIds, $marked);

        // Redirect to the unpaid invoices page with only remaining (unpaid) invoices selected
        return redirect()
            ->route('manage-invoice-system', ['invoices' => $remaining])
            ->with('success', "Invoices #" . implode(', ', $marked) . " have been marked as paid successfully.");
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()
            ->back()
            ->with('error', "Failed to mark invoices as paid: {$e->getMessage()}");
    }
}
}


