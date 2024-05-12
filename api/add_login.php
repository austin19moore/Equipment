<?php

$email = $_REQUEST['email'];
$password = $_REQUEST['password'];

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

if ($password == NULL || empty($password)) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "password cannot be empty",
        'Action' => "Provide password"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

// check length
if (strlen($email) > 40) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "Email cannot be greater than 40 characters",
        'Action' => "Check email"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

if (strlen($password) > 40) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "password cannot be greater than 40 characters",
        'Action' => "Check password"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
}

// check if email exists
if (emailExists($email, $dblink) == true) {
    header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output = array(
        'Status' => "ERROR",
        'MSG' => "login already exists",
        'Action' => "None"
    );
    $responeData=json_encode($output);
    echo $responeData;
    die();
 }

$hash = hash('sha256', $password);


$sql="INSERT INTO logins(email, password, hash) VALUES('$email', '$password', '$hash')";

$rst=$dblink->query($sql) or
    unknownError();

header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output = array(
    'Status' => "SUCCESS",
    'MSG' => "Added login",
    'Action' => "None",
    'Data' => array('hash' => $hash)
);
$responeData=json_encode($output);
echo $responeData;
die();


?>