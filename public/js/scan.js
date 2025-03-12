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
    if (message != scannedID) {
        let divs = document.querySelectorAll("#qr-shaded-region > div");
        for (let div of divs) {
            div.style.backgroundColor = "#00ff00";
        }
        setTimeout(() => {
            let divs = document.querySelectorAll("#qr-shaded-region > div");
            for (let div of divs) {
                div.style.backgroundColor = "white";
            }
        }, 500);
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
                return response.json();
            })
            .then((data) => {
                if (usedMODE == "INFO") {
                    alert("All package data (test): " + data.package);
                } else {
                    alert("Status (test):" + data.message);
                }
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
});
