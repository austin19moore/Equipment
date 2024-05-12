<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Equipment</title>
<link href="../assets/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/font-awesome.min.css">
<link rel="stylesheet" href="../assets/css/owl.carousel.css">
<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
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
                    <a href="#" class="navbar-brand">Modify Equipment Database</a>
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
                        $serial = $_GET['serialnumber'];
                        if ($serial == NULL && !isset($_GET['msg'])) {
                            redirect("modifySearch.php");
                        }

                        // errors
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="serialLength")
                        {
                            echo '<div class="alert alert-danger" role="alert">New serial number too long!</div>';
                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="newSerialExists")
                        {
                            echo '<div class="alert alert-danger" role="alert">New serial number already exists!</div>';
                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="currentSerialError")
                        {
                            echo '<div class="alert alert-danger" role="alert">Current serial number does not exist!</div>';
                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                        {
                            echo '<div class="alert alert-danger" role="alert">Please enter a device, manufacturer, or new serial number!</div>';
                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                        {
                            echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';

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
                <h3>Modifying Serial number:</h3>
                <h5><?php echo $serial?></h5>
                <br>
               <form method="POST" action="">
                    <div class="form-group">
                        <h3>Modify Equipment:</h3>
                        <br>
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device" id="device">
                              <?php
                              echo '<option value="">None</option>';
                              foreach($devices as $key=>$value)
                                   echo '<option value="'.$value.'">'.$value.'</option>';
                              ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer" id = "manufacturer">
                            <?php
                                echo '<option value="">None</option>';
                              foreach($manufacturers as $key=>$value)
                                   echo '<option value="'.$value.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                            <label for="exampleActive">Active:</label>
                            <input type="checkbox" checked name="active" value="active" aria-label="Checkbox for active">
                        </div>
                    <div class="form-group">
                        <label for="exampleSerial">New Serial Number (or empty for no change):</label>
                        <input type="text" class="form-control" id="serialInput" name="newserialnumber">
                    </div>
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Modify Equipment</button>
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
        $newserialNumber=trim($_POST['newserialnumber']);
        $active = $_POST['active'];
        if ($active == NULL || empty($active)) {
            $active = 0;
        } else {
            $active = 1;
        }
        
        if (strlen($newserialNumber) > 70) {
          redirect("modify.php?msg=serialLength&serialnumber=".$serial);
        }


        $url = "WEB_ADDRESS/api/modify_equipment?";
        $data = array(
            "device" => $device,
            "manufacturer" => $manufacturer,
            "currentserialnumber" => $serial,
            "newserialnumber" => $newserialNumber,
            'active' => $active
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
            redirect("modifySearch.php?msg=unresponsive");
        }

        // success/errors
        if ($result['Status'] == "SUCCESS") {
            redirect("index.php?msg=ModifySuccess&serialnumber=".$serial);
        } else {

            if ($result['MSG'] == "Current serial number does not exist") {
                redirect("modify.php?msg=currentSerialError&serialnumber=".$serial);
            } else if ($result["MSG"] == "New serial number already exists") {
                redirect("modify.php?msg=newSerialExists&serialnumber=".$serial);
            } else if ($result["MSG"] == "At least one of device, manufacturer, or new serial number must be provided") {
                redirect("modify.php?msg=empty&serialnumber=".$serial);
            } else {
                redirect("modify.php?msg=unknown&serialnumber=".$serial);
            }

        }

    }
        
?>