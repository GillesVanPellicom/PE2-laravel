<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Package Label</title>
    <style>
        @page {
            size: landscape;
            margin: 20px;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background: #fff;
        }
        .label-container {
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            width: 750px;
            height: 340px;
            position: relative;
            background: linear-gradient(to right, #fff, #f8fafc);
            margin: 20px auto;
        }
        .tags-container {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }
        .address-section {
            width: 60%;
            float: left;
            position: relative;
            z-index: 1;
        }
        .qr-section {
            width: 35%;
            float: right;
            border-left: 2px solid #e2e8f0;
            padding-left: 20px;
            height: 290px;
            text-align: center;
            background: #fff;
            border-radius: 0 6px 6px 0;
        }
        .section {
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .section-title {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 8px;
            color: #2563eb;
            letter-spacing: 0.5px;
        }
        .address {
            font-size: 14px;
            line-height: 1.6;
            color: #1f2937;
        }
        .tracking-number {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 4px;
            color: #1f2937;
        }
        .signature-required {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
        }
        .priority-tag {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
        }
        .qr-code {
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 10px auto;
            width: fit-content;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="tags-container">
            {{-- <div class="priority-tag">
                Priority Shipping
            </div> --}}
            <div class="signature-required">
                Signature Required
            </div>
        </div>

        <div class="address-section">
            <!-- From Section -->
            <div class="section">
                <div class="section-title">From:</div>
                <div class="address">
                    @if(Auth::check())
                        {{ $customer->first_name }} {{ $customer->last_name }}<br>
                        {{ $customer_address->street }} {{ $customer_address->house_number }}<br>
                        {{ $customer_address->city->postcode }} {{ $customer_address->city->name }}<br>
                        {{ $customer_country->country_name }}
                    @else
                        {{$customer}}<br>
                        {{ $customer_address }}<br>
                        {{$customer_country}}

                    @endif

                </div>
            </div>

            <!-- To Section -->
            <div class="section">
                <div class="section-title">To:</div>
                <div class="address">
                    {{ $package->name }} {{ $package->lastName }}<br>
                    {{ $receiver_address->street }} {{ $receiver_address->house_number }}<br>
                    {{ $receiver_address->city->postcode }} {{ $receiver_address->city->name }}<br>
                    {{ $receiver_country->country_name }}
                </div>
            </div>
        </div>

        <div class="qr-section">
            <div class="section-title">Tracking Number</div>
            <div class="tracking-number">{{ $tracking_number }}</div>
            <div class="qr-code">
                <img src="data:image/png;base64,{{ $qr_code }}"
                     alt="Package QR Code"
                     style="width: 150px; height: 150px; display: block;">
            </div>
        </div>
    </div>
</body>
</html>
