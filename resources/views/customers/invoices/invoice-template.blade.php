<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Package Invoice</title>
    <style>
        @include('customers.invoices.invoice-style');
    </style>

</head>
<body>
    <table class="header">
        <tr>
            <td width="50%">
                <img src="{{asset('images/logo.png')}}" alt="Company Logo" width="150"/>
                <h2>Company Name</h2>
            </td>
            <td width="50%" class="company-info">
                <h3>INVOICE</h3>
                <p>Invoice #: INV-2025001</p>
                <p>Date: {{$invoice->created_at->format('Y-m-d')}}</p>
                <br>
                <p>Company Name</p>
                <p>Jan Pieter de Nayerlaan 5</p>
                <p>2860 Sint-Katelijne-Waver</p>
                <p>Belgium</p>
                <p>VAT: BE0123456789</p>
            </td>
        </tr>
    </table>

    <table class="invoice-details">
        <tr>
            <td width="50%">
                <strong>Bill To:</strong>
                {{$company->company_name}}<br>
                {{$company_address->street}} {{$company_address->house_number}}
                @if($company_address->bus_number)
                 - {{$company_address->bus_number}}
                @endif
                <br>
                {{$company_address->city->postcode}} {{$company_address->city->name}}, {{$company_address->city->country->country_name}}<br>
                {{$company->VAT_Number}}
            </td>
            <td width="50%">

            </td>
        </tr>
    </table>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Package ID</th>
                <th>Delivery Method</th>
                <th>Weight Class</th>
                <th>Country</th>
                <!-- <th>Amount</th> -->
                <th>Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $package)
            <tr>
                <td>{{$package->reference}}</td>
                <td>{{$package->deliveryMethod->name}}</td>
                <td>{{$package->weightClass->name}} ({{$package->weightClass->weight_min}}kg - {{$package->weightClass->weight_max}}kg)</td>
                <td>
                @if($package->deliveryMethod->requires_location)
                    {{ $package->destinationLocation->address->city->country->country_name }}
                @else
                    {{ $package->address->city->country->country_name }}
                @endif
                </td>
                <!-- <td>1</td> -->
                <td>€{{ number_format((float)$package->weight_price + (float)$package->delivery_price, 2) }}</td>            
                  <td class="text-right">€{{ (float)$package->weight_price + (float)$package->delivery_price }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td width="60%">Subtotal:</td>
            <td class="text-right">€{{$subtotal}}</td>
        </tr>
        @if($discount > 0)
        <tr>
            <td>Discount:</td>
            <td class="text-right">- €{{$discount}}</td>
        </tr>
        @endif
        <tr>
            <td>VAT (21%):</td>
            <td class="text-right">€{{$VAT}}</td>
        </tr>
        <tr class="total-row">
            <td>Total:</td>
            <td class="text-right">€{{$total}}</td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>Payment Terms:</strong> Net 30 days</p>
        <p><strong>Payment Details:</strong> Bank Transfer to IBAN: BE12 3456 7890 1234</p>
        <p>Thank you for choosing [Company Name] for your delivery needs!</p>
    </div>
</body>
</html>