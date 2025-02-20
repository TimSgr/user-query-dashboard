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
        foreach($all_queries_for_user as $data){ ?>
        <?php error_log(print_r($data, true)); ?>
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                <td class="px-6 py-4 sessionID">
                    <?php echo htmlspecialchars($data["sid"]) ?>
                </td>
                <td class="px-6 py-4">
                    <?php echo htmlspecialchars($data["search"]) ?>
                </td>
                <td class="px-6 py-4">
                    <?php echo htmlspecialchars($data["ts"]) ?>
                </td>
            </tr>
        <?php
        }
        $html = ob_get_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'html' => $html]);
        exit;
    }
} else {
    error_log("not allowed");
}
