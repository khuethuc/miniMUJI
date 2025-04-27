<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user'){
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch ($page) {
        /* Guest and Customer */
        case 'home':
            include "src/views/user/home/home.php";
            break;
        case 'products':
            include "src/views/user/products/products.php";
            break;
        case 'product-details':
            include "src/views/user/products/product-details.php";
            break;
        case 'contact':
            include "src/views/user/contact/contact.php";
                break;
        case 'login':
            include "src/views/authentication/login.php";
            break;
        case 'register':
            include "src/views/authentication/register.php";
            break;
        /* Customer */
        case 'logout':
            include "src/views/authentication/logout.php";
            break;
        case 'cart':
            include "src/views/user/carts/cart.php";
            break;
        default:
            echo "Page not found.";
            break;
    }
        
}
else{
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch ($page) {
        case 'dashboard':
            include "src/views/admin/dashboard/dashboard.php";
            break;
        case 'products-admin':
            include "src/views/admin/products/products-admin.php";
            break;
        case 'add-product':
            include "src/views/admin/products/add-product.php";
            break;
        case 'edit-product':
            include "src/views/admin/products/edit-product.php";
            break;
        case 'logout':
            include "src/views/authentication/logout.php";
            break;
        case 'view-product':
            include "src/views/admin/products/view-product.php";
                break;
        default:
            echo "Page not found.";
            break;
    }
}

?>
