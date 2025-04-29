document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('image');
    const fileLabel = document.getElementById('file-name');

    // Khi vừa load trang
    const existingFileName = fileInput.getAttribute('data-filename');
    if (existingFileName) {
        fileLabel.textContent = existingFileName;
    }

    // Khi người dùng chọn file mới
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            fileLabel.textContent = file.name;
        } else {
            fileLabel.textContent = existingFileName || "Upload product image";
        }
    });
});

document.getElementById('edit-product').addEventListener('submit', function (e) {
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
            alert("Product editted.");
            window.location.href = '/minimuji/view-product/' + data.id;
        } 
        else {
            alert("Error: " + (data.message || "Unknown error"));
        }
    })
    .catch(err => {
        console.error("Upload failed", err);
        alert("An unexpected error occurred.");
    });
    
});
