<?php
// DB Zugangsdaten
$servername = "db";
$username = "root";
$password = "password";
$database = "userdata";

// Initiales Datenbank testing
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Datenbankverbindung fehlgeschlagen: " . $conn->connect_error);
}

create_latest_search_table($conn);
$per_page = 50;


// Optionale SQL Queries
$most_searched_query = execute_query($conn, "
SELECT search 
FROM streetsearch_log 
GROUP BY search 
ORDER BY COUNT(*) 
DESC LIMIT 1", true);

$most_searched_query_amount = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM streetsearch_log 
GROUP BY search 
ORDER BY COUNT(*) 
DESC LIMIT 1", true);

$most_searched_query_amount = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM streetsearch_log 
GROUP BY search 
ORDER BY COUNT(*) 
DESC LIMIT 1", true);

// Haupt SQL Anfrage um Daten in der Tabelle verfügbar zu machen
$searches = execute_query($conn, "
SELECT * 
FROM latest_streetsearch 
AS s1 
ORDER BY ts DESC
LIMIT " . (int)$per_page);

// SQL Anfrage um Gesamtzahl der session ids zu bekommen
$total_sessions = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM latest_streetsearch
", true);

// SQL Anfrage um gesamtzahl der queries zu bekommen
$total_searches = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM streetsearch_log
", true);

// Pagination Variables
$current_page = 1;
$total_pages = ceil($total_sessions / $per_page);
$offset = ($current_page - 1) * $per_page;

// Funktion um eine neue Tabelle zu erstellen für die latest query strings bei der suche
function create_latest_search_table($conn)
{
    $db_action = $conn->prepare("
    CREATE TABLE IF NOT EXISTS latest_streetsearch AS 
    SELECT s1.sid, s1.search, s1.ts 
    FROM streetsearch_log AS s1
    JOIN (
        SELECT sid, MAX(ts) AS max_ts
        FROM streetsearch_log
        GROUP BY sid
    ) AS latest ON s1.sid = latest.sid AND s1.ts = latest.max_ts;
    ");
    $db_action->execute();
}

// Funktion um prepare und execute Funktionen einfacher auzuführen
function execute_query($mysql_connection, $query, $single_value = false)
{
    $stmt = $mysql_connection->prepare($query);

    if (!$stmt) {
        error_log("SQL-Fehler: bei prepare(): " . $mysql_connection->error);
        return $single_value ? null : [];
    }

    if (!$stmt->execute()) {
        error_log("SQL-Fehler: bei SQL execute(): " . $stmt->error);
        return $single_value ? null : [];
    }

    $result = $stmt->get_result();

    if (!$result) {
        error_log("SQL-Fehler: Kein gültiges Result");
        return $single_value ? null : [];
    }

    if ($single_value) {
        $row = $result->fetch_assoc();
        return $row ? reset($row) : null;
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

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
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="/assets/js/main.js"></script>
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
</body>
</html>
<?php

// Datenbank Verbindung wieder schließen
$conn->close();

?>