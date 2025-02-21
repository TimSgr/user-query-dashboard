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

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suchstatistik</title>
</head>
<body>
    <!-- TODO: Taildwind shouldnt be loaded full -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="flex flex-col gap-4 p-4 justify-center items-center">
        <h2 class="text-center text-2xl font-bold">User Query Übersicht</h2>
        <p class="text-center text-xl">
            <a class="underline" href="documentation.html">Dokumentation</a>
        </p>
        <hr class="w-1/3 text-center"></hr>
    </div>

    <div class="flex gap-4 justify-center p-4 w-1/3 m-auto" id="switcher_section">
        <ul class="flex text-sm w-full font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400">
            <li class="me-2 w-1/2">
                <a href="#" aria-current="active" class="w-full inline-block p-4 text-blue-600 bg-gray-100 rounded-t-lg active dark:bg-gray-800 dark:text-blue-500" id="plain">Plain Text</a>
            </li>
            <li class="me-2 w-1/2">
                <a href="#" aria-current="inactive" class="w-full inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-gray-300" id="visual">Visuell</a>
            </li>
        </ul>
    </div>
    <div class="general_data flex gap-4 justify-center p-4" id="plain_section">
        <div class="amount_queries flex flex-col justify-center text-center">
            <h2>Gesamte Suchanfragen:</h2>
            <p id="full_search_amount"><?php echo htmlspecialchars($total_searches); ?></p>
            <h2 class="mt-4">Davon nach Postleitzahl</h2> 
            <p id="postcode_search_amount"><?php echo htmlspecialchars($number_postcodes); ?></p>
        </div>
        <div class="max_queries_user"></div>
        <div class="average_queries_user flex flex-col justify-center text-center">
            <h2>Ø Suchanfragen pro Nutzer:</h2>
            <p><?php echo htmlspecialchars(str_replace(".", ",", $average_query_per_user)); ?></p>
        </div>
        <div class="most_searched_query flex flex-col justify-center text-center">
            <h2>Most searched query:</h2>
            <p>"<?php echo htmlspecialchars(trim($most_searched_query)); ?>" wurde <?php echo htmlspecialchars($most_searched_query_amount); ?> mal gesucht</p>
        </div>
    </div>
    <div class="general_data flex hidden gap-4 justify-center p-4 m-auto" id="visual_section">
        <canvas id="myChart" style="width:100%;max-width:600px; max-height: 200px;"></canvas>
    </div>
    

    <div class="table_wrapper relative overflow-x-auto">
        <table id="results" class="table-auto w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Session ID</th>
                    <th scope="col" class="px-6 py-3">Search Query</th>
                    <th scope="col" class="px-6 py-3">Timestamp</th>
                    <th scope="col" class="px-6 py-3">Anfragen</th>
                    <th scope="col" class="px-6 py-3"></th>
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
    <div class="pagination-controls"></div>
    <div id="sessionDetailPopup" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-4/5 p-6 relative">
            <button id="closePopup" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-4xl cursor-pointer">&times;</button>
            <h2 class="text-2xl font-semibold mb-4 text-center">Session Details</h2>
            <div id="sessionData" class="flex justify-center flex-col">
                <p class="text-gray-600 text-center">Lädt...</p>
            </div>
        </div>
    </div>
    <script src="/assets/js/main.js" asp-append-version="true"></script>
    <script src="/assets/js/charts.js" asp-append-version="true"></script>
</body>
</html>
<?php

// Datenbank Verbindung wieder schließen
$conn->close();

?>