<?php

$device = $_REQUEST['device'];
$manufacturer = $_REQUEST['manufacturer'];
$serialNumber = $_REQUEST['serialnumber'];
$active = $_REQUEST['allowinactive'];
if ($active == NULL) {
    $active = "> 0";
} else if ($active == "0" || $active == 0) {
    $active = "> 0";
} else if ($active == "1" || $active == 1) {
    $active = ">= 0";
} else {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
    $output[]='MSG: Active must be empty, 0, or 1';
    $output[]='Action: Check active';
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (($device == NULL || empty($device)) && ($serialNumber == NULL || empty($serialNumber)) && ($manufacturer == NULL || empty($manufacturer))) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Atleast one of device, manufacturer, or new serial number must be provided",
        'Action' => "Provide device, manufacturer, or serialnumber"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ((!empty($device)) && !(deviceExists($device, $dblink))) {
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


if ((!empty($manufacturer)) && !(manufacturerExists($manufacturer, $dblink))) {
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

if ($active == "> 0") {
    
    if (!empty($device) && deviceActive($device, $dblink) == false) {
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output = array(
            'Status' => "ERROR",
            'MSG' => "Device exists, but is inactive",
            'Action' => "Check allowinactive OR modify_device"
        );
        $responeData=json_encode($output);
        echo $responeData;
        die();
    }

    if (!empty($manufacturer) && manufacturerActive($manufacturer, $dblink) == false) {
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output = array(
            'Status' => "ERROR",
            'MSG' => "Manufacturer exists, but is inactive",
            'Action' => "Check allowinactive OR modify_manufacturer"
        );
        $responeData=json_encode($output);
        echo $responeData;
        die();
    }

}

$sql = "";
if (empty($serialNumber)) {

        if (empty($device)) {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.manufacturer_id = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer') AND serials.active ".$active." LIMIT 1000";
        } else if (empty($manufacturer)) {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.device_id = (SELECT `auto_id` FROM `devices` WHERE name = '$device') AND serials.active ".$active." LIMIT 1000";
        } else {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.device_id = (SELECT `auto_id` FROM `devices` WHERE name = '$device') AND serials.manufacturer_id = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer') AND serials.active ".$active." LIMIT 1000";
        }

} else {

        if (empty($device) && empty($manufacturer)) {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serial_number LIKE '%$serialNumber%' AND serials.active ".$active." LIMIT 1000";
        } else if (empty($device)) {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.manufacturer_id = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer') AND serial_number LIKE '%$serialNumber%' AND serials.active ".$active." LIMIT 1000";
        } else if (empty($manufacturer)) {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.device_id = (SELECT `auto_id` FROM `devices` WHERE name = '$device') AND serial_number LIKE '%$serialNumber%' AND serials.active ".$active." LIMIT 1000";
        } else {
            $sql = "SELECT serials.auto_id AS autoid, manufacturers.auto_id, devices.auto_id, devices.name AS devicename, manufacturers.name AS manufacturername, serials.serial_number FROM serials JOIN devices ON devices.auto_id = serials.device_id JOIN manufacturers ON manufacturers.auto_id = serials.manufacturer_id WHERE serials.device_id = (SELECT `auto_id` FROM `devices` WHERE name = '$device') AND serials.manufacturer_id = (SELECT `auto_id` FROM `manufacturers` WHERE name = '$manufacturer') AND serial_number LIKE '%$serialNumber%' AND serials.active ".$active." LIMIT 1000";
        }
}
logQuery("", $_SERVER['REMOTE_ADDR'], "SEARCH", $dblink);
$result=$dblink->query($sql) or
    unknownError();

if ($result->num_rows < 1) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "No equipment found",
        'Action' => "None"
    );

    $responeData=json_encode($output);
    echo $responeData;
    die();
}

$equipment=array();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
    $equipment[$data['autoid']]=array("device" => $data['devicename'], "manufacturer" => $data['manufacturername'], "serialnumber" => $data['serial_number']);

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Retrieved equipment.",
    'Action' => "None",
    'Data' => $equipment
);

$responeData=json_encode($output);
echo $responeData;
die();
?>