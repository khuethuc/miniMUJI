function deleteProduct(productId) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    fetch(`/minimuji/src/views/admin/products/delete-product.php?id=${productId}`)
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.href='/minimuji/products-admin'
            }
        })
        .catch(err => {
            alert(`An unexpected error occurred: ${err.message}`);
            console.error("Error details:", err);
        });
}