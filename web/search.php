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
                    <a href="#" class="navbar-brand">Search Equipment Database</a>
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
                        $active = $_REQUEST['allowinactive'];
                        
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                         {
                              echo '<div class="alert alert-danger" role="alert">Atleast one field must be non-empty!</div>';

                         }
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                        {
                            echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unresponsive")
                        {
                            echo '<div class="alert alert-danger" role="alert">Server did not respond!</div>';
                        }

                        if ($active == NULL) {
                            $active = 0;
                        } else if ($active == "true") {
                            $active = 1;
                        } else {
                            $active = 0;
                        }

                        
                        if ($active == 0) {
                            $allowInactives = 1;
                        } else {
                            $allowInactives = 0;
                        }

                        $url = "WEB_ADDRESS/api/get_devices?";
                        $data = array(
                            "active" => $allowInactives
                        );
                        $data =  http_build_query($data);

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
                            redirect("search.php?msg=unknown&allowinactive=".$_REQUEST['allowinactive']);
                        }
                        $devices = $result['Data'];
                        curl_close($ch);


                        // manufacturers
                        $url = "WEB_ADDRESS/api/get_manufacturers?";
                        $data = array(
                            "active" => $allowInactives
                        );
                        $data =  http_build_query($data);

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
                            redirect("search.php?msg=unknown");
                        }
                        $manufacturers = $result['Data'];
                        curl_close($ch);

                   ?>

                    <form method="POST" action="">
                    <h3>Search Equipment:</h3>

                    <?php
                        if ($_REQUEST['allowinactive'] == "true") {
                            echo '<h5>Allowing inactive</h5>';
                        }
                    ?>

                    <br>
                    <div class="form-group">
                        <label for="exampleDevice">Device:</label>
                        <select class="form-control" name="device" id="device">
                        <option value="">None</option>
                            <?php
                                foreach($devices as $key=>$value)
                                    echo '<option value="'.$value.'">'.$value.'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleManufacturer">Manufacturer:</label>
                        <select class="form-control" name="manufacturer" id = "manufacturer">
                         <option value="">None</option>
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
                        <button type="submit" class="btn btn-primary" name="submit" value="submit">Search Equipment</button>
                        <?php

                            if ($_REQUEST['allowinactive'] == "true") {
                                echo '<button class="btn btn-primary"><a href="search.php" style="color:white">Disable inactive</a></button>';
                            } else {
                                echo '<button class="btn btn-primary"><a href="search.php?allowinactive=true" style="color:white">Allow inactive</a></button>';
                            }

                        ?>
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


               if (empty($device) && empty($manufacturer) && empty($serialNumber)) {
                    redirect("search.php?msg=empty".$_REQUEST['allowinactive']);
               }

        redirect("results.php?device=$device&manufacturer=$manufacturer&serialNumber=$serialNumber&active=$active");
    }
?>