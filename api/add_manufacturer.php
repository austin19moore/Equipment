<?php
$name = $_REQUEST['name'];

if ($name == NULL || empty($name)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Name cannot be empty",
        'Action' => "Provide name"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (strlen($name) > 20) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Manufacturer name cannot be greater than 20 characters",
        'Action' => "Check name"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (manufacturerExists($name, $dblink) == true && manufacturerActive($name, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Manufacturer already exists, but is disabled",
        'Action' => "modify_manufacturer"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (manufacturerExists($name, $dblink) == true) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Manufacturer already exists",
        'Action' => "None"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

$sql="INSERT INTO manufacturers(name, active) VALUES('$name', '1')";
logQuery("", $_SERVER['REMOTE_ADDR'], "APPEND", $dblink);
$rst=$dblink->query($sql) or
    unknownError();

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Added manufacturer",
    'Action' => "None"
);
$responeData=json_encode($output);
echo $responeData;
die();
?>