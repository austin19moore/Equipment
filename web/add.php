<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Equipment</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
<?php
    error_reporting(E_ALL);
?>
<link rel="stylesheet" href="../assets/css/templatemo-style.css">
</head>
<body>
<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
          <div class="container">
               <div class="navbar-header">
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                         <span class="icon icon-bar"></span>
                    </button>
                    <a href="#" class="navbar-brand">Add New Equipment</a>
               </div>
               <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-nav-first">
                         <li><a href="index.php" class="smoothScroll">Home</a></li>
                         <li><a href="search.php" class="smoothScroll">Search Equipment</a></li>
                         <li><a href="add.php" class="smoothScroll">Add Equipment</a></li>
                         <li><a href="modify.php" class="smoothScroll">Modify Equipment</a></li>
                    </ul>
               </div>
          </div>
     </section>
     <section id="home">
          </div>
     </section>
     <section id="feature">
          <div class="container">
               <div class="row">
                   
                   <?php 
                    include("../functions.php");
                        // display errors
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceExists")
                        {
                            echo '<div class="alert alert-danger" role="alert">Serial Number already exists!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="SerialLength")
                        {
                            echo '<div class="alert alert-danger" role="alert">Serial Number is too long!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                        {
                            echo '<div class="alert alert-danger" role="alert">Serial Number cannot be empty!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                        {
                            echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unresponsive")
                        {
                            echo '<div class="alert alert-danger" role="alert">Server did not respond!</div>';
                            die();
                        }

                        // curl options
                        $url = "WEB_ADDRESS/api/get_devices";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        // get devices
                        $result = curl_exec($ch);
                        if ($result == NULL) {
                            redirect("index.php?msg=unresponsive");
                        }

                        $devices = json_decode($result, true);
                        $devices = $devices["Data"];

                        
                        // get manufacturers
                        $url = "WEB_ADDRESS/api/get_manufacturers";
                        curl_setopt($ch, CURLOPT_URL, $url);
                        $result = curl_exec($ch);
                        if ($result == NULL) {
                            redirect("index.php?msg=unresponsive");
                        }

                        $manufacturers = json_decode($result, true);
                        $manufacturers = $manufacturers["Data"];
                        curl_close($ch);
                   
                   ?>
                    <form method="POST" action="">
                    <h3>Add Equipment:</h3>
                    <br>
                    <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device" id="device">
                            <?php
                                foreach($devices as $key=>$value)
                                    echo '<option value="'.$value.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer" id = "manufacturer">
                            <?php
                                foreach($manufacturers as $key=>$value)
                                    echo '<option value="'.$value.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSerial">Serial Number:</label>
                        <input type="text" class="form-control" id="serialInput" name="serialnumber">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Add Equipment</button>
                        <button class="btn btn-primary"><a href="appendDevice.php" style="color:white">Add New Device</a></button>
                        <button class="btn btn-primary"><a href="appendManufacturer.php" style="color:white">Add New Manufacturer</a></button>
                        <button class="btn btn-primary"><a href="modifyDevice.php" style="color:white">Modify Device</a></button>
                        <button class="btn btn-primary"><a href="modifyManufacturer.php" style="color:white">Modify Manufacturer</a></button>
                   </form>
               </div>
          </div>
     </section>
</body>
</html>
<?php
    
    if (isset($_POST['submit']))
    {
        $device=$_POST['device'];
        $manufacturer=$_POST['manufacturer'];
        $serialNumber=trim($_POST['serialnumber']);

        if (empty($serialNumber)) {
            redirect("add.php?msg=empty");
        }

        if (strlen($serialNumber) >= 70) {
            redirect("add.php?msg=SerialLength");
        }


        $url = "WEB_ADDRESS/api/add_equipment?";
        $data = array(
            "device" => $device,
            "manufacturer" => $manufacturer,
            "serialnumber" => $serialNumber
        );
        $data =  http_build_query($data);

        // post data to url using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $result = curl_exec($ch);
        $result = json_decode($result, true);

        curl_close($ch);
        if ($result['Status'] == NULL) {
            redirect("index.php?msg=unresponsive");
        }

        // success/errors
        if ($result["Status"] == "SUCCESS" && $result['MSG'] == "Added equipment") {
            redirect("index.php?msg=EquipmentAdded");
        } else {

            if ($result["MSG"] == "Serial number already exists") {
                redirect("add.php?msg=DeviceExists");
            } else {
                redirect("add.php?msg=unknown");
            }

        }

    }

?>