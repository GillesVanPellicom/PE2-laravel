<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('company_id', auth()->id())->get();
        return view('Packages.customer-list', compact('customers'));
    }
}