document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image');
    const fileLabel = fileInput.nextElementSibling; // chính là <span> ngay sau <input>

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            fileLabel.textContent = file.name;
        } else {
            fileLabel.textContent = "Upload product image";
        }
    });
});

document.getElementById('add-product').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        console.log("Server JSON:", data);

        if (data.status && data.status.trim() === "success") {
            alert("New product added!");
            window.location.href = '/minimuji/products-admin';
        } else {
            alert("Error: " + (data.message || "Unknown error"));
        }
    })
    .catch(err => {
        console.error("Upload failed", err);
        alert("An unexpected error occurred.");
    });
    
});