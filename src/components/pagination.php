<?php 
function changePageNumber($page_number) {
    // Get current query parameters
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $sort_price = isset($_GET['sort_price']) ? $_GET['sort_price'] : '';

    // Construct the clean URL
    if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user') {
        $url = '/minimuji/products';
    } 
    else {
        $url = '/minimuji/products-admin';
    }
    if ($category) {
        $url .= '/category/' . urlencode($category);
    }
    if ($sort_price) {
        $url .= '/sort_price/' . urlencode($sort_price);
    }
    $url .= '/page/' . $page_number;

    return $url;
}

function renderPageBtn($i, $current_page) {
    $link = changePageNumber($i);
    if ($i == $current_page) {
        return '<button class="pgn-button active">' . $i . '</button>';
    } 
    else {
        return '<button class="pgn-button" onclick="window.location.href=\'' . $link . '\'">' . $i . '</button>';
    }
}

function renderPagination($total_pages) {
    $page = isset($_GET['pgn']) ? (int)$_GET['pgn'] : 1;
    $display_page = ceil($total_pages / 3);
    $step = ceil(($display_page - 1) / 2);
    $start = max(2, $page - $step);
    $end = min($total_pages - 1, $page + $step);

    echo '<div class="pagination">';

    // Back Button
    if ($page > 1) {
        echo '<button class="pgn-button" onclick="window.location.href=\'' . changePageNumber($page - 1) . '\'">&lt; Back</button>';
    } 
    else {
        echo '<button class="pgn-button disabled" disabled>&lt; Back</button>';
    }

    // Page Numbers
    if ($total_pages <= 5) {
        for ($i = 1; $i <= $total_pages; $i++) {
            echo renderPageBtn($i, $page);
        }
    } 
    else {
        // Always display page 1
        echo renderPageBtn(1, $page);
        
        // Display dots
        if (($start - 1) > 1) {
            echo '<button class="pgn-button dots" disabled>...</button>';
        }

        // Display step nearest pages 
        for ($i = $start; $i <= $end; $i++) {
            echo renderPageBtn($i, $page);
        }
        
        // Display dots
        if (($total_pages - $end) > 1) {
            echo '<button class="pgn-button dots" disabled>...</button>';
        }

        // Always display final page
        echo renderPageBtn($total_pages, $page);
    }

    // Next Button
    if ($page < $total_pages) {
        echo '<button class="pgn-button" onclick="window.location.href=\'' . changePageNumber($page + 1) . '\'">Next &gt;</button>';
    } 
    else {
        echo '<button class="pgn-button disabled" disabled>Next &gt;</button>';
    }

    echo '</div>';
}
?>