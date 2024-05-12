<?php
$oldname = $_REQUEST['oldname'];
$newname = $_REQUEST['newname'];
$active = $_REQUEST['active'];

if ($active == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "active cannot be empty",
        'Action' => "Provide active"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($active != "0" && $active != "1") {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "active must be either 0 or 1",
        'Action' => "Check active"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($oldname == NULL) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Device oldname cannot be empty",
        'Action' => "Provide oldname"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (strlen($newname) > 12) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Device newname cannot be greater than 12 characters",
        'Action' => "Check newname"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (deviceExists($oldname, $dblink) == false) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Old device does not exist",
        'Action' => "add_device"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (deviceExists($newname, $dblink) == true) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "New device already exists",
        'Action' => "None"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}
if ($newname == NULL || empty($newname)) {
    $sql="UPDATE devices SET active = '$active' WHERE name = '$oldname';";
} else {
    $sql="UPDATE devices SET name = '$newname', active = '$active' WHERE name = '$oldname';";
}
logQuery("", $_SERVER['REMOTE_ADDR'], "MODIFY", $dblink);
$rst=$dblink->query($sql) or
    unknownError();

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Modified device",
    'Action' => "None"
);
$responeData=json_encode($output);
echo $responeData;
die();
?>