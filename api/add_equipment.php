<?php

$device = $_REQUEST['device'];
$manufacturer = $_REQUEST['manufacturer'];
$serialNumber = $_REQUEST['serialnumber'];

// check empty/null
if ($serialNumber == NULL || empty($serialNumber)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Serial number cannot be empty",
        'Action' => "Check serialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($device == NULL || empty($device)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Missing device",
        'Action' => "Provide device"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($manufacturer == NULL || empty($manufacturer)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Missing manufacturer",
        'Action' => "Provide manufacturer"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

// check serial_number length
if (strlen($serialNumber) > 70) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Serial number cannot be greater than 70 characters",
        'Action' => "Check serialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

// check if device exists
 if (deviceExists($device, $dblink) == false) {
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

// check if manufacturer exists
if (manufacturerExists($manufacturer, $dblink) == false) {
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

if (deviceActive($device, $dblink) == false) {
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

if (manufacturerActive($manufacturer, $dblink) == false) {
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


if (serialExists($serialNumber, $dblink) == false)
{
    $sql="Insert into `serials` (`device_id`,`manufacturer_id`,`serial_number`) values ((SELECT `auto_id` FROM `devices` WHERE name = '$device'), (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer'),'$serialNumber')";
    $dblink->query($sql) or
        unknownError();
    logQuery($serialNumber, $_SERVER['REMOTE_ADDR'], "ADD", $dblink);
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "Added equipment",
        'Action' => "None"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
} else {
    // duplicate
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Serial number already exists",
        'Action' => "Check serialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}
    

?>