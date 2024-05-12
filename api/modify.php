<?php

$device = $_REQUEST['device'];
$manufacturer = $_REQUEST['manufacturer'];
$currentserialNumber = $_REQUEST['currentserialnumber'];
$newserialNumber = $_REQUEST['newserialnumber'];
$active = $_REQUEST['active'];

if ($device == NULL && $manufacturer == NULL && $newserialNumber == NULL && $active == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "At least one of device, manufacturer, active, or new serial number must be provided",
        'Action' => "Provide device, manufacturer, active, or newserialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($active == NULL) {
    $active = "1";
} else if ($active == "0" || $active == 0) {
    $active = "0";
} else if ($active == "1" || $active == 1) {
    $active = "1";
} else {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Active must be empty, 0, or 1",
        'Action' => "Check active"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($currentserialNumber == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Current Serial number cannot be empty",
        'Action' => "Check currentserialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (strlen($newserialNumber) > 70) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "New serial number cannot be greater than 70 characters",
        'Action' => "Check newserialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($device != NULL && deviceExists($device, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Device does not exist",
        'Action' => "add_device"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($manufacturer != NULL && manufacturerExists($manufacturer, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Manufacturer does not exist",
        'Action' => "add_manufacturer"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (serialExists($currentserialNumber, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Current serial number does not exist",
        'Action' => "add_device"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($device != NULL && deviceActive($device, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Device set to inactive",
        'Action' => "modify_device"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($manufacturer != NULL && manufacturerActive($manufacturer, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Manufacturer set to inactive",
        'Action' => "modify_manufacturer"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($newserialNumber != NULL && serialExists($newserialNumber, $dblink) == true) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "New serial number already exists",
        'Action' => "None"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

$sql="";
               
if ($newserialNumber == NULL || empty($newserialNumber)) {
    if (empty($device) && empty($manufacturer)) {
        $sql = "UPDATE `serials` SET `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else if (empty($device)) {
            $sql = "UPDATE `serials` SET `manufacturer_id` = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer'), `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else if (empty($manufacturer)) {
            $sql = "UPDATE `serials` SET `device_id` = (SELECT `auto_id` FROM `devices` WHERE name = '$device'), `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else {
            $sql = "UPDATE `serials` SET `device_id` = (SELECT `auto_id` FROM `devices` WHERE name = '$device'), `manufacturer_id` = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer'), `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    }
    logQuery($currentserialNumber, $_SERVER['REMOTE_ADDR'], "MODIFY", $dblink);
} else {

    if (empty($device) && empty($manufacturer)) {
            $sql = "UPDATE `serials` SET `serial_number` = '$newserialNumber', `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else if (empty($manufacturer)) {
            $sql = "UPDATE `serials` SET `device_id` = (SELECT `auto_id` FROM `devices` WHERE name = '$device'), `serial_number` = '$newserialNumber', `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else if (empty($device)) {
            $sql = "UPDATE `serials` SET `manufacturer_id` = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer'), `serial_number` = '$newserialNumber', `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    } else {
            $sql = "UPDATE `serials` SET `device_id` = (SELECT `auto_id` FROM `devices` WHERE name = '$device'), `manufacturer_id` = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer'), `serial_number` = '$newserialNumber', `active` = '$active' WHERE `serial_number` = '$currentserialNumber'";
    }
    logQuery($newserialNumber, $_SERVER['REMOTE_ADDR'], "MODIFY", $dblink);
}

$dblink->query($sql) or
    unknownError();

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Modified equipment",
    'Action' => "None"
);
$responseData=json_encode($output);
echo $responseData;
die();

?>