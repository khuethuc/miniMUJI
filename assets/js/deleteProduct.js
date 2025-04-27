function deleteProduct(productId) {
    if (!confirm("Are you sure you want to delete this job?")) return;

    fetch(`src/views/admin/products/delete-product.php?id=${productId}`)
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.href='?page=products-admin'
            }
        })
        .catch(err => {
            alert("An unexpected error occurred.");
            console.error(err);
        });
}