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
                <p>Invoice #: INV-2024001</p>
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
                <strong>Bill To:</strong><br>
                Customer Name<br>
                Company Name<br>
                Street Address<br>
                City, Country<br>
                VAT Number
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
                <th>Amount</th>
                <th>Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>PKG001</td>
                <td>Home Address</td>
                <td>Light (0-2kg)</td>
                <td>Belgium</td>
                <td>1</td>
                <td>€15.00</td>
                <td class="text-right">€15.00</td>
            </tr>
            <tr>
                <td>PKG002</td>
                <td>Pickup Point</td>
                <td>Medium (2-5kg)</td>
                <td>Netherlands</td>
                <td>2</td>
                <td>€20.00</td>
                <td class="text-right">€40.00</td>
            </tr>
            <tr>
                <td>PKG003</td>
                <td>Parcel Locker</td>
                <td>Heavy (5-10kg)</td>
                <td>Germany</td>
                <td>1</td>
                <td>€25.00</td>
                <td class="text-right">€25.00</td>
            </tr>
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td width="60%">Subtotal:</td>
            <td class="text-right">€80.00</td>
        </tr>
        <tr>
            <td>VAT (21%):</td>
            <td class="text-right">€16.80</td>
        </tr>
        <tr class="total-row">
            <td>Total:</td>
            <td class="text-right">€101.80</td>
        </tr>
    </table>

    <div class="footer">
        <p><strong>Payment Terms:</strong> Net 30 days</p>
        <p><strong>Payment Details:</strong> Bank Transfer to IBAN: BE12 3456 7890 1234</p>
        <p>Thank you for choosing [Company Name] for your delivery needs!</p>
    </div>
</body>
</html>