<?php
require_once "database_connection.php";

function render_single_search_result($session_id, $search_query, $timestamp){
    ob_start();
    ?>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
        <td class="px-6 py-4">
            <?php echo htmlspecialchars($session_id) ?>
        </td>
        <td class="px-6 py-4">
            <?php echo htmlspecialchars($search_query) ?>
        </td>
        <td class="px-6 py-4">
            <?php echo htmlspecialchars($timestamp) ?>
        </td>
        <td class="px-6 py-4">
            <button class="bg-blue-500 sessionID hover:bg-blue-700 cursor-pointer text-white font-bold py-2 px-4 rounded-full" value="<?php echo $session_id ?>">    
                Verlauf
            </button>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 100;
$offset = ($page - 1) * $per_page;

$searches = execute_query($conn, "
    SELECT * FROM latest_streetsearch
    ORDER BY ts DESC
    LIMIT $per_page OFFSET $offset
");

$total_sessions = execute_query($conn, "
    SELECT COUNT(*) AS total FROM latest_streetsearch
", true);
$total_pages = ceil($total_sessions / $per_page);

ob_start();
foreach ($searches as $search) {
    echo render_single_search_result($search["sid"], $search["search"], $search["ts"]);
}
$table_html = ob_get_clean();

$pagination_html = "<div class='flex justify-center space-x-2 p-4 overflow-auto'>";

if ($page > 1) {
    $pagination_html .= "<button class='pagination-btn px-4 py-2 bg-blue-500 text-white rounded' data-page='" . ($page - 1) . "'>&laquo; Vorherige</button>";
}

$range = 5;

if ($page > 1 + $range) {
    $pagination_html .= "<button class='pagination-btn px-4 py-2 bg-gray-300 rounded' data-page='1'>1</button>";
    $pagination_html .= "<span class='px-2 py-2'>...</span>";
}

$start = max(1, $page - $range);
$end = min($total_pages, $page + $range);

for ($i = $start; $i <= $end; $i++) {
    $active = ($i == $page) ? 'bg-gray-900 text-white' : 'bg-gray-300';
    $pagination_html .= "<button class='pagination-btn px-4 py-2 $active rounded' data-page='$i'>$i</button>";
}

if ($page < $total_pages - $range) {
    $pagination_html .= "<span class='px-2 py-2'>...</span>";
    $pagination_html .= "<button class='pagination-btn px-4 py-2 bg-gray-300 rounded' data-page='$total_pages'>$total_pages</button>";
}

if ($page < $total_pages) {
    $pagination_html .= "<button class='pagination-btn px-4 py-2 bg-blue-500 text-white rounded' data-page='" . ($page + 1) . "'>Nächste &gt;</button>";
}

$pagination_html .= "</div>";

echo json_encode(['success' => true, 'html' => $table_html, 'pagination' => $pagination_html]);
exit;
?>
