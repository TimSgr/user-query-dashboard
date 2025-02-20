<?php

$database_file = "database_connection.php";
if(!file_exists($database_file)){
    die;
}else{
    require_once $database_file;
}

function render_single_search_result($session_id, $search_query, $timestamp){
    ob_start();
    ?>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
        <td class="px-6 py-4 sessionID">
            <?php echo htmlspecialchars($session_id) ?>
        </td>
        <td class="px-6 py-4">
            <?php echo htmlspecialchars($search_query) ?>
        </td>
        <td class="px-6 py-4">
            <?php echo htmlspecialchars($timestamp) ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suchstatistik</title>
</head>
<body>
    <div class="general_data flex gap-4">
        <div class="amount_queries">
            <h2>Gesamte Suchanfragen:</h2>
            <p><?php echo htmlspecialchars($total_searches); ?></p>
        </div>
        <div class="max_queries_user"></div>
        <div class="average_queries_user"></div>
        <div class="most_searched_query">
            <h2>Most searched query:</h2>
            <p>"<?php echo htmlspecialchars(trim($most_searched_query)); ?>" wurde <?php echo htmlspecialchars($most_searched_query_amount); ?> gesucht</p>
        </div>
    </div>
    <div class="table_wrapper relative overflow-x-auto">
        <table id="results" class="table-auto w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Session ID</th>
                    <th scope="col" class="px-6 py-3">Search Query</th>
                    <th scope="col" class="px-6 py-3">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($searches as $search) {
                        echo render_single_search_result($search["sid"], $search["search"], $search["ts"]);
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <?php

    echo "<div class='pagination-controls'>";
    if ($current_page > 1) {
        echo "<button class='pagination-btn' data-page='" . ($current_page - 1) . "'>&laquo; Vorherige</button>";
    } else {
        echo "<button class='pagination-btn' disabled>&laquo; Vorherige</button>";
    }

    $range = 2;
    $start = max(1, $current_page - $range);
    $end = min($total_pages, $current_page + $range);

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? 'active' : '';
        echo "<button class='pagination-btn $active' data-page='$i'>$i</button>";
    }

    if ($current_page < $total_pages) {
        echo "<button class='pagination-btn' data-page='" . ($current_page + 1) . "'>Nächste &raquo;</button>";
    } else {
        echo "<button class='pagination-btn' disabled>Nächste &raquo;</button>";
    }
    echo "</div>";
    ?>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>
<?php

// Datenbank Verbindung wieder schließen
$conn->close();

?>