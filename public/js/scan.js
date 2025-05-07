let MODE = null;
let QRActive = false;
let scannedID = null;
let actionText = {
    INFO: "Package Info",
    IN: "Scan Package In",
    OUT: "Scan Package Out",
    DELIVER: "Deliver Package",
    RETURN: "Mark a package for return",
    FAILED: "Mark the delivery as failed",
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

function getPackageInfo(id) {
    callAction(id, "INFO", (data) => {
        document.getElementById("infoModal").innerHTML = data.message;
        openInfoModal();
    });
}

function undoAction(id) {
    callAction(id, "UNDO", (data) => {
        if (data.success) return;
        document.getElementById("infoModal").innerHTML = data.message;
        openInfoModal();
    });
}

function confirmAction(id) {
    callAction(id, "RETURN", (data) => {
        closeInfoModal();
        if (data.success) return;
        document.getElementById("infoModal").innerHTML = data.message;
        openInfoModal();
    });
}

function showCheckmark() {
    const checkmarkContainer = document.createElement("div");
    checkmarkContainer.id = "checkmark";
    checkmarkContainer.style.position = "absolute";
    checkmarkContainer.style.top = "50%";
    checkmarkContainer.style.left = "50%";
    checkmarkContainer.style.transform = "translate(-50%, -50%)";
    checkmarkContainer.style.width = "100px";
    checkmarkContainer.style.height = "100px";
    checkmarkContainer.style.zIndex = "20";

    const checkmarkLine1 = document.createElement("div");
    checkmarkLine1.style.position = "absolute";
    checkmarkLine1.style.backgroundColor = "#00FF00";
    checkmarkLine1.style.width = "20px";
    checkmarkLine1.style.height = "50px";
    checkmarkLine1.style.top = "50px";
    checkmarkLine1.style.left = "15px";
    checkmarkLine1.style.transform = "rotate(-45deg)";
    checkmarkLine1.style.borderRadius = "2px";

    const checkmarkLine2 = document.createElement("div");
    checkmarkLine2.style.position = "absolute";
    checkmarkLine2.style.backgroundColor = "#00FF00";
    checkmarkLine2.style.width = "20px";
    checkmarkLine2.style.height = "85px";
    checkmarkLine2.style.top = "20px";
    checkmarkLine2.style.left = "50px";
    checkmarkLine2.style.transform = "rotate(45deg)";
    checkmarkLine2.style.borderRadius = "2px";

    checkmarkContainer.appendChild(checkmarkLine1);
    checkmarkContainer.appendChild(checkmarkLine2);
    document.getElementById("qr-shaded-region").appendChild(checkmarkContainer);

    setTimeout(() => {
        document
            .getElementById("qr-shaded-region")
            .removeChild(checkmarkContainer);
    }, 2000);
}

function callAction(id, mode, callback) {
    body = { mode: mode };
    if (id.includes("REF")) {
        body.reference = id;
    } else {
        body.package_id = id;
    }
    
    fetch(scanQrRoute, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify(body),
    })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (data.success) showCheckmark();
            callback(data);
        });
}

function scanAction(message, mode) {
    let divs = document.querySelectorAll("#qr-shaded-region > div");
    for (let div of divs) {
        if (div.id == "checkmark") continue;
        div.style.backgroundColor = "#00ff00";
    }

    setTimeout(() => {
        let divs = document.querySelectorAll("#qr-shaded-region > div");
        for (let div of divs) {
            if (div.id == "checkmark") continue;
            div.style.backgroundColor = "white";
        }
    }, 500);

    if (mode == "RETURN") {
        document.getElementById("infoModal").innerHTML =
            '<div class="bg-white p-6 rounded-lg shadow-lg w-80 relative flex flex-col"><h2 class="text-xl font-semibold text-center mb-3"> Are you sure you want to mark this package as returned? </h2> <button class="flex flex-col items-center justify-center bg-red-800 hover:bg-red-900 text-white p-4 rounded-xl focus:outline-none" onclick="confirmAction(' +
            message +
            ')"> Yes </button></h2> <button class="flex flex-col items-center justify-center bg-gray-800 mt-2 hover:bg-gray-900 text-white p-4 rounded-xl focus:outline-none" onclick="closeInfoModal()"> No </button></div>';
        openInfoModal();
        return;
    }

    callAction(message, mode, (data) => {
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("lastPackages").innerHTML =
                    xhttp.responseText;
            }
        };
        xhttp.open("GET", getLastPackagesRoute, true);
        xhttp.setRequestHeader("X-CSRF-TOKEN", csrf);
        xhttp.send();

        if (data.success) {
            if (mode == "INFO") {
                document.getElementById("infoModal").innerHTML = data.message;
                openInfoModal();
            }
            return;
        }
        document.getElementById("infoModal").innerHTML = data.message;
        openInfoModal();
    });
}

function scan(message) {
    if (!QRActive) return;
    let usedMODE = MODE;
    if (message != scannedID) {
        scanAction(message, usedMODE);
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

function openManualEntryModal() {
    document.getElementById("manualEntryModal").style.display = "flex";
}

function closeManualEntryModal() {
    document.getElementById("manualEntryModal").style.display = "none";
}

function submitManualEntry(event) {
    event.preventDefault();
    const reference = document.getElementById("manualReference").value;
    closeManualEntryModal();
    scanAction("REF" + reference, MODE);
}

function fetchLastPackages() {
    fetch(getLastPackagesRoute)
        .then((response) => response.text())
        .then((html) => {
            document.getElementById("lastPackages").innerHTML = html;
        })
        .catch((error) =>
            console.error("Error fetching last packages:", error)
        );
}
