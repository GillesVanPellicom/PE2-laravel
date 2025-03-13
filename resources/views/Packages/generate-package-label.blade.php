<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Package Label</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
        }
        .label-container {
            border: 2px solid #000;
            padding: 15px;
            width: 600px;
            height: 400px;
            background: white;
            display: flex;
            gap: 20px;
        }
        .left-section {
            flex: 1.5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .right-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 1px dashed #000;
            padding-left: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .address {
            font-size: 14px;
            line-height: 1.6;
        }
        .qr-placeholder {
            width: 200px;
            height: 200px;
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
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="left-section">
            <!-- From Section -->
            <div class="section">
                <div class="section-title">From:</div>
                <div class="address">
                    John Smith<br>
                    123 Sender Street<br>
                    Shipping City, ST 12345<br>
                    United States
                </div>
            </div>

            <!-- To Section -->
            <div class="section">
                <div class="section-title">To:</div>
                <div class="address">
                    Jane Doe<br>
                    456 Receiver Avenue<br>
                    Delivery Town, ST 67890<br>
                    United States
                </div>
            </div>
        </div>

        <div class="right-section">
            <!-- QR Code Section -->
            <div class="section">
                <div class="section-title">Tracking Number:</div>
                <div class="tracking-number">1Z 999 999 99 9999 999 9</div>
                <div class="qr-placeholder">
                    QR Code<br>
                    Place Here<br>
                    (200x200)
                </div>
            </div>
        </div>
    </div>
</body>
</html>