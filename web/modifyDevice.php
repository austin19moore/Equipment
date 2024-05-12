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
                    <a href="#" class="navbar-brand">Modify Device</a>
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
                         
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                        {
                            echo '<div class="alert alert-danger" role="alert">Old device name cannot be empty!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="deviceLength")
                        {
                            echo '<div class="alert alert-danger" role="alert">Device name too long!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceExists")
                        {
                            echo '<div class="alert alert-danger" role="alert">New device name already exists!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="OldNotExist")
                        {
                            echo '<div class="alert alert-danger" role="alert">Old device does not exist!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                        {
                            echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';

                        }

                        $url = "WEB_ADDRESS/api/get_devices?";
                        $data = array(
                            "active" => "0"
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
                        if ($result['Status'] == NULL) {
                            redirect("index.php?msg=unresponsive");
                        }
                        if ($result['Status'] == "ERROR") {
                            redirect("modifyDevice.php?msg=unknown");
                        }
                        $devices = $result['Data'];
                        curl_close($ch);

                    ?>
                    <form method="post" action="">

                        <label for="exampleNew">Modify Device:</label>
                        <br><br>
                        <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="oldname" id="oldname">
                            <?php
                                foreach($devices as $key=>$value)
                                    echo '<option value="'.$value.'">'.$value.'</option>';
                            ?>
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleActive">Active:</label>
                            <input type="checkbox" checked name="active" value="active" aria-label="Checkbox for active">
                        </div>
                        <div class="form-group">
                            <label for="exampleSerial">Name (or empty for no change):</label>
                            <input type="text" class="form-control" id="newDeviceInput" name="newname">
                        </div>
                         <button type="submit" class="btn btn-primary" name="submit" value="submit">Modify</button>
                    </form>
                    

               </div>
          </div>
     </section>
</body>
</html>

<?php

     if (isset($_POST['submit']))
    {
        $oldname=$_POST['oldname'];
        $newname=trim($_POST['newname']);
        $active=$_POST['active'];
        if ($active == NULL) {
            $active=0;
        } else {
            $active=1;
        }

            if (empty($oldname)) {
                redirect("modifyDevice.php?msg=empty");
            }

            if (strlen($newname) > 12) {
                redirect("modifyDevice.php?msg=deviceLength");
            }

           
            $url = "WEB_ADDRESS/api/modify_device?";
        $data = array(
            "oldname" => $oldname,
            "newname" => $newname,
            "active" => $active
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
            redirect("modifyDevice.php?msg=unresponsive");
        }

        // success/errors
        if ($result["Status"] == "SUCCESS") {
            redirect("index.php?msg=DeviceModified");
        } else {

            if ($result["MSG"] == "New device already exists") {
                redirect("modifyDevice.php?msg=DeviceExists");
            } else if ($result["MSG"] == "Old device does not exist") {
                redirect("modifyDevice.php?msg=OldNotExist");
            } else if ($result["MSG"] == "Device name too long") {
                redirect("modifyDevice.php?msg=deviceLength");
            } else if ($result["MSG"] == "Device names cannot be empty") {
                redirect("modifyDevice.php?msg=empty");
            } else {
                redirect("modifyDevice.php?msg=unknown");
            }

        }

    }

?>