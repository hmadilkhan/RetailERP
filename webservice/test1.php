<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
require "crud.php";
date_default_timezone_set("Asia/Karachi");

error_reporting(E_ALL);

$crud = new Crud();

// echo getDepartment(7);
function getDepartment($id)
{
    if (!empty($id) && $id > 0) {
        $result = $GLOBALS['crud']->runQuery("SELECT a.* FROM inventory_department a INNER JOIN inventory_general b on b.department_id = a.department_id and b.status = 1 and a.company_id = '$id'");

        if (!empty($result) && sizeof($result) > 0) {
            return json_encode($result);
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

?>