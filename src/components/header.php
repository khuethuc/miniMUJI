<header>
    <!-- Left header -->
    <div class = "left-header">
        <!-- Logo -->
        <div class = "logo">
            <a href = "/minimuji/home">miniMUJI</a>
        </div>
        <!-- Navigation -->
        <?php 
        if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user'){ ?>
            <nav>
                <ul>
                    <li><a href="/minimuji/home">Home</a></li>
                    <li><a href="/minimuji/products">Products</a></li>
                    <li><a href="/minimuji/contact">Contact</a></li>
                </ul>
            </nav>
        <?php } 
        else { ?>
            <nav>
                <ul>
                    <li><a href="/minimuji/dashboard">Home</a></li>
                    <li><a href="/minimuji/products-admin">Products</a></li>
                    <li><a href="/minimuji/orders-admin">Orders</a></li>
                    <li><a href="/minimuji/customers">Customers</a></li>
                </ul>
            </nav>
        <?php } ?>
    </div>

    <!-- Mid header -->
    <div class = "mid-header">
        <!-- Search -->
        <?php
        if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user') { ?>
            <form onsubmit="redirectSearch(); return false;" autocomplete="off">
                <div class="search-container">
                    <input 
                        type="text" 
                        placeholder="Search products..." 
                        id="keyword"
                        name="keyword" 
                        class="search-input" 
                        autocomplete="off"
                        value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                    >
                    <div class="suggestions" id="suggestion-box" style="display: none;"></div>

                    <button type="submit" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                        </svg>
                    </button>
                </div>
            </form>
        <?php } ?>
    </div>

    <!-- Right header -->
    <div class = "right-header">
        <!-- Icons -->
        <?php 
        /* Guest - Authentication */
        if (!isset($_SESSION['role'])) { ?>
            <div class = "button">
                <button class = "white-button" onclick="window.location.href='/minimuji/login'">Login</button>
                <button class = "red-button" onclick="window.location.href='/minimuji/register'">Register</button>
            </div>
        <?php } 
        /* Customer - Account and cart */
        else if ($_SESSION['role'] == 'user'){ ?>
            <div class = "icons">
                <!-- Cart -->
                <button onclick="window.location.href='/minimuji/cart/<?=$_SESSION['id']?>'" class = "icon-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="white" class="bi bi-cart2" viewBox="0 0 16 16">
                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l1.25 5h8.22l1.25-5zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                    </svg>
                </button>
                <!-- Account -->
                <button onclick = "toggleMenu()" class = "icon-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </button>
                <div id = "account-menu" class = "account-menu">
                    <ul>
                        <li><button onclick = "window.location.href='/minimuji/my-orders'">My Orders</button></li>
                        <li><button onclick = "window.location.href='/minimuji/logout'" class = "logout-button">Log Out</button></li>
                    </ul>
                </div>
            </div>
            
            
        <?php }
        /* Admin - Account */
        else { ?>
            <div class="icons">
                <button onclick = "toggleMenu()" class = "icon-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                    </svg>
                </button>
                <div id = "account-menu" class = "account-menu">
                    <ul>
                        <li><button onclick = "window.location.href='/minimuji/logout'" class = "logout-button">Log Out</button></li>
                    </ul>
                </div>    
            </div>       
        <?php } ?>
    </div>
</header>

<script>
    // Hàm để toggle menu tài khoản
    function toggleMenu() {
        const accountMenu = document.getElementById('account-menu');
        // Kiểm tra nếu menu đang hiển thị, nếu có thì ẩn, nếu không thì hiển thị
        if (accountMenu.style.display === 'block') {
            accountMenu.style.display = 'none';
        } 
        else {
            accountMenu.style.display = 'block';
        }
    }

    // Đóng menu nếu người dùng click ra ngoài
    document.addEventListener('click', function(event) {
        const accountMenu = document.getElementById('account-menu');
        const accountButton = document.querySelector('button[onclick="toggleMenu()"]');

        // Kiểm tra nếu người dùng click ngoài menu và ngoài icon tài khoản
        if (!accountMenu.contains(event.target) && !accountButton.contains(event.target)) {
            accountMenu.style.display = 'none';
        }
    });
</script>

<script src = "/minimuji/assets/js/search-bar.js"></script>