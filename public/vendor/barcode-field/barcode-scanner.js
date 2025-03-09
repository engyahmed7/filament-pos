const codeReader = new ZXing.BrowserMultiFormatReader();
let isScanning = false;

function openScannerModal() {
    // Open the Filament modal
    window.dispatchEvent(
        new CustomEvent("open-modal", {
            detail: { id: "barcode-scanner-modal" },
        })
    );
}

function closeScannerModal() {
    // Close the Filament modal
    window.dispatchEvent(
        new CustomEvent("close-modal", {
            detail: { id: "barcode-scanner-modal" },
        })
    );
    stopScanning(); // Make sure to stop the camera when the modal closes
}

function startScanner(selectedDeviceId) {
    codeReader.decodeFromVideoDevice(
        selectedDeviceId,
        "scanner",
        (result, err) => {
            const scanArea = document.querySelector(".scan-area");
            if (result) {
                document.getElementById("{{ $getId() }}").value = result.text; // Set barcode value
                scanArea.style.borderColor = "green";
                stopScanning(); // Optionally stop scanning after successful read
                closeScannerModal(); // Close the modal after successful scan
            } else if (err && !(err instanceof ZXing.NotFoundException)) {
                console.error(err);
            } else {
                scanArea.style.borderColor = "red";
            }
        }
    );
}

function stopScanning() {
    isScanning = false;
    const video = document.getElementById("scanner");
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
    }
    video.style.display = "none";
}

function startCamera() {
    codeReader
        .getVideoInputDevices()
        .then((videoInputDevices) => {
            const rearCamera = videoInputDevices.find(
                (device) =>
                    device.label.toLowerCase().includes("back") ||
                    device.label.toLowerCase().includes("rear")
            );
            const selectedDeviceId = rearCamera
                ? rearCamera.deviceId
                : videoInputDevices[0].deviceId;

            navigator.mediaDevices
                .getUserMedia({
                    video: { deviceId: { exact: selectedDeviceId } },
                })
                .then(function (stream) {
                    const video = document.getElementById("scanner");
                    video.srcObject = stream;
                    video.style.display = "block"; // Ensure the video element is visible
                    startScanner(selectedDeviceId);
                })
                .catch(function (err) {
                    console.error("Error accessing the camera: ", err);
                    alert("Camera access is required to scan barcodes.");
                });
        })
        .catch((err) => {
            console.error(err);
        });
}

// Listen for modal opening and start camera
window.addEventListener("open-modal", (event) => {
    console.log(event);
    if (event.detail.id === "barcode-scanner-modal") {
        console.log("Modal opened, starting camera");
        startCamera();
    }
});

// Listen for modal closing and stop camera
window.addEventListener("close-modal", (event) => {
    if (event.detail.id === "barcode-scanner-modal") {
        console.log("Modal closed, stopping camera");
        stopScanning();
    }
});

document.addEventListener("keydown", function (e) {
    let barcode = "";
    let reading = false;

    if (e.key === "Enter" && reading) {
        fetch("/api/scan-barcode", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ barcode: barcode }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Add product to cart UI
                    addToCart(data.product);
                }
            });

        reading = false;
        barcode = "";
    } else {
        reading = true;
        barcode += e.key;
    }
});
