<?php

$email = $_REQUEST['email'];
$hash = $_REQUEST['hash'];

// check empty/null
if ($email == NULL || empty($email)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "email cannot be empty",
        'Action' => "Provide email"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if ($hash == NULL || empty($hash)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "hash cannot be empty",
        'Action' => "Provide hash"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}


$sql = "SELECT `email`, `password`, `hash` FROM `logins` WHERE `hash` = '$hash' AND `email` = '$email'";
logQuery($serialnumber, $_SERVER['REMOTE_ADDR'], "VIEW", $dblink);
$result=$dblink->query($sql) or
    unknownError();

if ($result->num_rows<1)
{
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "ERROR",
    'MSG' => "Password not found",
    'Action' => "add_login"
);
$responeData=json_encode($output);
echo $responeData;
die();
} 

$password = "";
while ($data=$result->fetch_array(MYSQLI_ASSOC)) {
    $password=$data['password'];
}

// return
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Found password",
    'Action' => "None",
    'Data' => array('password' => $password)
);
$responeData=json_encode($output);
echo $responeData;
die();


?>