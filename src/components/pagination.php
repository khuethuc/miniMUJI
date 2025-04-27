<?php 
function changePageNumber($page_number) {
    $params = $_GET;
    $params['pgn'] = $page_number;
    return '?' . http_build_query($params);
}

function renderPageBtn($i, $current_page) {
    $link = changePageNumber($i);
    if ($i == $current_page) {
        return '<button class = "pgn-button active">' . $i . '</button>';
    } 
    else {
        return '<button class = "pgn-button" onclick = "window.location.href=\'' . $link . '\'">' . $i . '</button>';
    }
}

function renderPagination($total_pages) {
    $page = isset($_GET['pgn']) ? (int)$_GET['pgn'] : 1;
    // $page = max(1, min($page, $total_pages));

    echo '<div class = "pagination">';

    // Back Button
    if ($page > 1) {
        echo '<button class = "pgn-button" onclick = "window.location.href=\'' . changePageNumber($page - 1) . '\'">&lt; Back</button>';
    } 
    else {
        echo '<button class = "pgn-button disabled" disabled>&lt; Back</button>';
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

        if ($page > 5) {
            echo '<button class="pgn-button dots" disabled>...</button>';
        }

        // Display 5 nearest pages 
        for ($i = max(2, $page - 2); $i <= min($total_pages - 1, $page + 2); $i++) {
            echo renderPageBtn($i, $page);
        }

        if ($page < $total_pages - 4) {
            echo '<button class="pgn-button dots" disabled>...</button>';
        }

        // Always display final page
        echo renderPageBtn($total_pages, $page);
    }

    // Next Button
    if ($page < $total_pages) {
        echo '<button class = "pgn-button" onclick="window.location.href=\'' . changePageNumber($page + 1) . '\'">Next &gt;</button>';
    } 
    else {
        echo '<button class = "pgn-button disabled" disabled>Next &gt;</button>';
    }

    echo '</div>';
}
?>