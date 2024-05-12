<?php

$serialnumber = $_REQUEST['serialnumber'];
$active = NULL;
$autoid = NULL;
$device = NULL;
$manufacturer = NULL;

if ($serialnumber == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "serialnumber cannot be empty",
        'Action' => "Provide serialnumber"
    );
    $responseData=json_encode($output);
    echo $responseData;
    die();
}

if (serialExists($serialnumber, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "serial number does not exist",
        'Action' => "add_equipment"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

$sql = "SELECT `auto_id`, `device_id`, `manufacturer_id`, `serial_number`, `active` FROM serials WHERE serial_number = '$serialnumber';";
logQuery($serialnumber, $_SERVER['REMOTE_ADDR'], "VIEW", $dblink);
$result=$dblink->query($sql) or
    unknownError();


$equipment=array();
while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
    $autoid=$data['auto_id'];
    $device = $data['device_id'];
    $manufacturer = $data['manufacturer_id'];
    $serialnumber=$data['serial_number'];
    $active = $data['active'];
}


if ($autoid == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "No equipment found",
        'Action' => "None"
    );
    $responseData=json_encode($output);
    echo $responseData;
    die();
}


$sql = "SELECT `auto_id`, `name`, `active` FROM devices WHERE auto_id = '$device';";
$result=$dblink->query($sql) or
    unknownError();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
    $device = $data['name'];

$sql = "SELECT `auto_id`, `name`, `active` FROM manufacturers WHERE auto_id = '$manufacturer';";
$result=$dblink->query($sql) or
    unknownError();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
    $manufacturer = $data['name'];

$equipment = array(
    "auto_id" => $autoid,
    "device" => $device,
    "manufacturer" => $manufacturer,
    "serialnumber" => $serialnumber,
    "active" => $active
);


if ($equipment['auto_id'] == NULL || $equipment['serialnumber'] == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "No equipment found",
        'Action' => "None"
    );
    $responseData=json_encode($output);
    echo $responseData;
    die();
}

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Got equipment",
    'Action' => "None",
    'Data' => $equipment
);
$responseData=json_encode($output);
echo $responseData;
die();
?>