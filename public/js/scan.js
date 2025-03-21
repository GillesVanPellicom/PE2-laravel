let MODE = null;
let QRActive = false;
let scannedID = null;
let actionText = {
    INFO: "Package Info",
    IN: "Scan Package In",
    OUT: "Scan Package Out",
    DELIVER: "Deliver Package",
};

function toggleDetails(div) {
    let details = div.nextElementSibling;
    let icon = div.querySelector("i");
    if (details.style.display == "flex") {
        details.style.display = "none";
        icon.style.transform = "rotate(0deg)";
    } else {
        details.style.display = "flex";
        icon.style.transform = "rotate(90deg)";
    }
}

function chooseAction(action) {
    MODE = action;
    document.getElementById("current_action").innerText = actionText[action];
    QRActive = true;
    document.getElementById("actionModal").style.display = "none";
}

function openModal() {
    QRActive = false;
    scannedID = null;
    document.getElementById("actionModal").style.display = "flex";
}

function openInfoModal() {
    QRActive = false;
    scannedID = null;
    document.getElementById("infoModal").style.display = "flex";
}

function closeInfoModal() {
    QRActive = true;
    document.getElementById("infoModal").style.display = "none";
}

function scan(message) {
    if (!QRActive) return;
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
                let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("lastPackages").innerHTML = xhttp.responseText;
                    }
                };
                xhttp.open("GET", getLastPackagesRoute, true);
                xhttp.setRequestHeader("X-CSRF-TOKEN", csrf);
                xhttp.send();
                
                if (data.success) {
                    if (usedMODE == "INFO") {
                        document.getElementById("infoModal").innerHTML =
                            data.message;
                        openInfoModal();
                    }
                    return;
                }
                document.getElementById("infoModal").innerHTML = data.message;
                openInfoModal();
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
