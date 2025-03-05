<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <title>Courier Scan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #d32f2f;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .navbar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #d32f2f;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }
        .navbar i {
            font-size: 35px;
            color: white;
        }
        .navbar a:hover i {
            color: black;
        }
        #qr-reader {
            width: 400px;
            border: 3px solid #d32f2f;
            border-radius: 10px;
            margin: 20px 0;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }
        button {
            background-color: #d32f2f;
            color: white;
            padding: 10px 20px;
            border: none;
            margin: 10px;
            cursor: pointer;
        }

        

.back-arrow {
    position: absolute;
    left: 20px; /* Afstand van de rand */
    top: 50%;
    transform: translateY(-50%);
    color: white;
    font-size: 24px; 
    text-decoration: none;
}

.back-arrow:hover {
    color: black; /* Hover effect */
}
    </style>
</head>
<body>

<header class="header">
    <a href="{{ route('index.page') }}" class="back-arrow">
        <i class="fas fa-arrow-left"></i>
    </a>
    ShipCompany
</header>

<div id="qr-reader"></div>

<div id="packageModal" class="modal">
    <div class="modal-content">
        <h2>Package Details</h2>
        <p id="packageInfo"></p>
        <button onclick="updateStatus('delivered')">Delivered</button>
        <button onclick="updateStatus('distribution')">Send to Distribution Center</button>
        <button onclick="updateStatus('pickup')">Send to Pickup Point</button>
        <button onclick="closeModal()">Close</button>
    </div>
</div>

<nav class="navbar">
    <a href="{{ route('route.page') }}"><i class="fas fa-map"></i></a>
    <a href="{{ route('packages.page') }}"><i class="fas fa-box"></i></a>
    <a href="{{ route('scan.page') }}"><i class="fas fa-qrcode"></i></a>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qrReader = new Html5Qrcode("qr-reader");
        const packageModal = document.getElementById("packageModal");
        const packageInfo = document.getElementById("packageInfo");
        let scannedId = null;

        qrReader.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, (message) => {
            scannedId = message;
            packageInfo.textContent = "Package ID: " + scannedId;
            packageModal.style.display = "flex";
        });

        window.updateStatus = function (status) {
            fetch("{{ route('package.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    packageId: scannedId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                closeModal();
            });
        };

        window.closeModal = function () {
            packageModal.style.display = "none";
            qrReader.resume();
        };
    });
</script>

</body>
</html>
