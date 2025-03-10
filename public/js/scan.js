let MODE = null;
let QRActive = false;
let scannedID = null;

function chooseAction(action) {
    MODE = action;
    QRActive = true;
    document.getElementById("actionModal").style.display = "none";
}

function openModal() {
    QRActive = false;
    scannedID = null;
    document.getElementById("actionModal").style.display = "flex";
}

function scan(message) {
    let usedMODE = MODE;
    console.log("QR FOUND " + message);
    if (message != scannedID) {
        console.log("QR FOUND AND NOT FAILED " + message);
        fetch(scanQrRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf,
            },
            body: JSON.stringify({
                package_id: message,
                mode: MODE,
            }),
        })
            .then((response) => {
                console.log(response);
                return response.json();
            })
            .then((data) => {
                console.log(data);
                if (usedMODE == "INFO"){
                    
                }
                alert(data.message);
            });
    }
    scannedID = message;
    qrReader.resume();
}

document.addEventListener("DOMContentLoaded", function () {
    const qrReader = new Html5Qrcode("qr-reader");
    qrReader
        .start(
            {
                facingMode: "environment",
            },
            {
                fps: 10,
                qrbox: 250,
                aspectRatio: 1.0,
            },
            scan
        )
        .catch((err) => {});

    /*
            window.updateStatus = function(status) {
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

            window.closeModal = function() {
                packageModal.style.display = "none";
                qrReader.resume();
            }; */
});
