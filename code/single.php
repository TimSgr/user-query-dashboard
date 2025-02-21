<?php
error_log(print_r($_POST, true));
if ($_POST !== null && $_POST["action"] == "get_data" && !empty($_POST["sessionID"])) {
    $database_file = "database_connection.php";
    if(!file_exists($database_file)){
        die;
    }else{
        require_once $database_file;
    }

    $all_queries_for_user = all_queries_for_user($conn, $_POST["sessionID"]);
    error_log(print_r($all_queries_for_user, true));
    if($all_queries_for_user){
        error_log(print_r($all_queries_for_user, true));
        ob_start();
        echo '<div class="flex flex-col p-2">';
        echo '<h2 class="font-bold text-xl text-center">Query Verlauf für <span class="truncate break-all text-wrap">' . $_POST["sessionID"] . '</span></h2>';
        echo '<p class="text-center">Gesamanzahl Anfragen der Session: ' . count($all_queries_for_user) . '</p>';
        echo '</div>';
        echo '<div class="flex justify-center overflow-auto max-h-80">';
        echo '<table class="w-full"><thead><tr class="bg-gray-300 border-b dark:bg-gray-300 dark:border-gray-700 border-gray-200"><th class="w-1/2 px-6 py-4 text-left">Query</th><th class="w-1/2 px-6 py-4 text-left">Zeitstempel</th></tr></thead><tbody>';
        $i = 0;
        $prevTimestamp = null;
        $startTimestamp = null;

        foreach ($all_queries_for_user as $data) {
            ?>
            <tr class="bg-white border-b dark:bg-white dark:border-gray-700 border-gray-200">
                <td class="px-6 py-4 w-1/2">
                    <?php echo htmlspecialchars($data["search"]) ?>
                </td>
                <td class="px-6 py-4 w-1/2">
                    <?php 
                    $currentTimestamp = new DateTime($data["ts"]);
                    if ($prevTimestamp) {
                        $interval = $prevTimestamp->diff($currentTimestamp);
                        echo '<span class="text-green-600">+' . htmlspecialchars($interval->format('%H:%I:%S.%f')) . ' Sekunden</span>';
                    } else {
                        echo htmlspecialchars($data["ts"]);
                        $startTimestamp = $currentTimestamp;
                    }
                    $prevTimestamp = new DateTime($data["ts"]);
                    ?>
                </td>
            </tr>
            <?php
            $i++;
        }
        
        echo '</tbody></table>';
        echo '</div>';
        if ($startTimestamp && $prevTimestamp) {
            $totalTime = $startTimestamp->diff($prevTimestamp);
            echo '<div class="text-center mt-4 font-bold">Gesamtzeit der Session: ' . htmlspecialchars($totalTime->format('%H:%I:%S.%f')) . ' Sekunden</div>';
        }else{
            echo $startTimestamp;
        }
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'html' => $html]);
        exit;
    }
} else {
    error_log("not allowed");
}
