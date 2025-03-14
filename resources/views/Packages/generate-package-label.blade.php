<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Package Label</title>
    <style>
        @page {
            size: landscape;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .label-container {
            border: 2px solid #000;
            padding: 15px;
            width: 750px;
            height: 300px;
            position: relative;
        }
        .address-section {
            width: 60%;
            float: left;
        }
        .qr-section {
            width: 35%;
            float: right;
            border-left: 1px dashed #000;
            padding-left: 15px;
            height: 270px;
            text-align: center;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .address {
            font-size: 14px;
            line-height: 1.4;
        }
        .qr-placeholder {
            width: 150px;
            height: 150px;
            border: 1px dashed #000;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .tracking-number {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="address-section">
            <!-- From Section -->
            <div class="section">
                <div class="section-title">From:</div>
                <div class="address">
                    Company Name<br>
                    Company Street 123<br>
                    1234 AB Amsterdam<br>
                    Netherlands
                </div>
            </div>

            <!-- To Section -->
            <div class="section">
                <div class="section-title">To:</div>
                <div class="address">
                    {{ $package->name }} {{ $package->lastName }}<br>
                    {{ $receiver_address->street }} {{ $receiver_address->house_number }}<br>
                    {{ $receiver_address->city->name }}, {{ $receiver_address->city->postcode }}<br>
                    
                </div>
            </div>
        </div>

        <div class="qr-section">
            <div class="section-title">Tracking Number:</div>
            <div class="tracking-number">{{ $tracking_number }}</div>
            <div class="qr-code">
                <img src="data:image/png;base64,{{ $qr_code }}" 
                     alt="Package QR Code"
                     style="width: 150px; height: 150px; margin: 10px auto; display: block;">
            </div>
        </div>
    </div>
</body>
</html>