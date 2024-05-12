<?php

$active = $_REQUEST['active'];
if ($active == NULL) {
    $active = " WHERE active = 1";
} else if ($active == "0" || $active == 0) {
    $active = "";
} else if ($active == "1" || $active == 1) {
    $active = " WHERE active = 1";
} else {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "Active must be empty, 0, or 1",
        'Action' => "Check active"
    );  
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

$sql = "SELECT `name`, `auto_id` FROM `devices`".$active;
logQuery("", $_SERVER['REMOTE_ADDR'], "GET", $dblink);
$result=$dblink->query($sql) or
    unknownError();

$devices=array();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
    $devices[$data['auto_id']]=$data['name'];

if ($devices == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "No devices found",
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
    'MSG' => "Got devices",
    'Action' => "None",
    'Data' => $devices
);
$responseData=json_encode($output);
echo $responseData;
die();
?>