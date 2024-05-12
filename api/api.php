<?php
include("../functions.php");
$url=$_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$pathComponents=explode("/", trim($path, "/"));
$endPoint=$pathComponents[1];

$dblink=db_connect("equipment");
switch($endPoint) {

    case "add_equipment":
        include("add_equipment.php");
        break;
    case "add_device":
        include("add_device.php");
        break;
    case "add_manufacturer":
        include("add_manufacturer.php");
        break;
    case "modify_equipment":
        include("modify.php");
        break;
    case "list_equipment":
        include("list_equipment.php");
        break;
    case "get_devices":
        include("get_devices.php");
        break;
    case "get_manufacturers":
        include("get_manufacturers.php");
        break;
    case "view_equipment":
        include("view_equipment.php");
        break;
    case "modify_device":
        include("modify_device.php");
        break;
    case "modify_manufacturer":
        include("modify_manufacturer.php");
        break;
    case "add_login":
        include("add_login.php");
        break;
    case "check_login":
        include("check_login.php");
        break;
    default:
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output = array(
            'Status' => "ERROR",
            'MSG' => "Invalid or missing endpoint",
            'Action' => "None"
        );
        $responseData=json_encode($output);
        echo $responseData;
        break;
}

die();
?>