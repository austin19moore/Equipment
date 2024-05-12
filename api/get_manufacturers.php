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

$sql = "SELECT `name`, `auto_id` FROM `manufacturers`".$active;
logQuery("", $_SERVER['REMOTE_ADDR'], "GET", $dblink);
$result=$dblink->query($sql) or
    unknownError();

$manufacturers=array();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
    $manufacturers[$data['auto_id']]=$data['name'];

if ($manufacturers == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "SUCCESS",
        'MSG' => "No manufacturers found",
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
    'MSG' => "Got manufacturers",
    'Action' => "None",
    'Data' => $manufacturers
);
$responseData=json_encode($output);
echo $responseData;
die();
?>