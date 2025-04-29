// Hiển thị suggestion box
document.addEventListener('DOMContentLoaded', function () {
    const keywordInput = document.getElementById('keyword');
    const suggestionBox = document.getElementById('suggestion-box');

    keywordInput.addEventListener('input', function () {
        const keyword = this.value.trim();

        if (keyword.length < 1) {
            suggestionBox.style.display = 'none';
            suggestionBox.innerHTML = '';
            return;
        }

        fetch(`/minimuji/src/components/search-bar.php?keyword=${encodeURIComponent(keyword)}`)
            .then(response => response.json())
            .then(data => {
                suggestionBox.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item.name;
                        div.style.padding = '10px';
                        div.style.cursor = 'pointer';

                        div.addEventListener('click', function () {
                            keywordInput.value = item.name;
                            window.location.href = `/minimuji/product-details/${product['id']}`;
                        });

                        suggestionBox.appendChild(div);
                    });
                    suggestionBox.style.display = 'block';
                } 
                else {
                    const div = document.createElement('div');
                    div.textContent = 'No products found';
                    div.style.padding = '10px';
                    div.style.color = 'gray';
                    // div.style.textAlign = 'center';
                    div.style.cursor = 'default';
                    suggestionBox.appendChild(div);
                    suggestionBox.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                suggestionBox.style.display = 'none';
            });
    });

    document.addEventListener('click', function (e) {
        if (!suggestionBox.contains(e.target) && e.target !== keywordInput) {
            suggestionBox.style.display = 'none';
        }
    });
});

// Điều hướng sang trang display products
function redirectSearch() {
    const keyword = document.getElementById('keyword').value.trim();
    console.log('Keyword:', keyword); // Debugging: Check the keyword value

    if (keyword.length === 0) {
        alert('Please enter a search keyword!');
        return;
    }

    console.log('Redirecting to:', `/minimuji/products/keyword/${encodeURIComponent(keyword)}`); // Debugging: Check the URL
    window.location.href = `/minimuji/products/keyword/${encodeURIComponent(keyword)}`;
}