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

// Default per page Value damit search queries funktionieren

$per_page = 100;

// SQL Anfrage um Gesamtzahl der session ids zu bekommen
$total_sessions = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM latest_streetsearch
", true);

//Pagination Variablen
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_pages = ceil($total_sessions / $per_page);
$offset = ($current_page - 1) * $per_page;
$offset = isset($offset) ? (int)$offset : 0;

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
    ORDER BY ts DESC
    LIMIT $per_page OFFSET $offset
");


// SQL Anfrage um Gesamtzahl der queries zu bekommen
$total_searches = execute_query($conn, "
SELECT COUNT(*) 
AS total 
FROM streetsearch_log
", true);

// Durchschnittsanzahl der queries pro Session
$average_query_per_user = execute_query($conn, "
SELECT COUNT(*) / COUNT(DISTINCT sid) AS avg_queries_per_user 
FROM streetsearch_log;
", true);

// Gesamtsuche der Postleitzahlen
// Durch Zufall herausgefunden, dass man bei SQL auch Regex verwenden kann
$number_postcodes = execute_query($conn, "
SELECT COUNT(*) AS total 
FROM streetsearch_log 
WHERE search REGEXP '^[0-9]{5}$';
", true);




// SQL Anfrage um alle Queries für eine User Session zu bekommen
// Hier wird eine extra prepare und execute funktion erstellt, da es sonst zu sql injection kommen könnte
function all_queries_for_user($conn, $sessionID){
    $stmt = $conn->prepare("
        SELECT *  
        FROM streetsearch_log 
        WHERE sid = ?
        ORDER BY ts ASC
    ");
    if (!$stmt) {
        error_log("SQL-Fehler bei prepare all queries for user(): " . $conn->error);
        return [];
    }
    $stmt->bind_param("s", $sessionID);
    if (!$stmt->execute()) {
        error_log("SQL-Fehler bei execute(): " . $stmt->error);
        return [];
    }
    $result = $stmt->get_result();
    if (!$result) {
        error_log("SQL-Fehler: Kein gültiges Result");
        return [];
    }
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}


// Funktion um eine neue Tabelle zu erstellen für die latest query strings bei der suche
function create_latest_search_table($conn)
{
    $db_action = $conn->query("SHOW TABLES LIKE 'latest_streetsearch'");

    if ($db_action->num_rows == 0) {
        $db_action = $conn->query("
            CREATE TABLE latest_streetsearch 
            AS 
            SELECT 
                s1.sid, 
                s1.search, 
                s1.ts,
                search_count.total_searches
            FROM streetsearch_log AS s1
            JOIN (
                SELECT sid, COUNT(*) AS total_searches
                FROM streetsearch_log
                GROUP BY sid
            ) AS search_count ON s1.sid = search_count.sid
            JOIN (
                SELECT sid, MAX(ts) AS max_ts
                FROM streetsearch_log
                GROUP BY sid
            ) AS latest ON s1.sid = latest.sid AND s1.ts = latest.max_ts
        ");

        if (!$db_action) {
            error_log("SQL-Fehler beim Erstellen der Tabelle: " . $conn->error);
        }
    }
}

// Funktion um prepare und execute Funktionen einfacher auzuführen
function execute_query($mysql_connection, $query, $single_value = false)
{
    $stmt = $mysql_connection->prepare($query);

    if (!$stmt) {
        error_log("SQL-Fehler: bei prepare(): " . "query:". $query . $mysql_connection->error);
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
?>