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
                    <a href="#" class="navbar-brand">Add New Device</a>
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
                            echo '<div class="alert alert-danger" role="alert">Device name cannot be empty!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="deviceLength")
                        {
                            echo '<div class="alert alert-danger" role="alert">Device name too long!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceExists")
                        {
                            echo '<div class="alert alert-danger" role="alert">Name already exists!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                        {
                            echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';

                        }

                    ?>
                    <form method="post" action="">

                        <label for="exampleNew">New Device:</label>
                        <br>
                         <div class="form-group">
                         <label for="exampleName">Name:</label>
                         <input type="text" class="form-control" id="nameInput" name="name">
                         </div>
                         <button type="submit" class="btn btn-primary" name="submit" value="submit">Add</button>
                    </form>
                    

               </div>
          </div>
     </section>
</body>
</html>

<?php

     if (isset($_POST['submit']))
    {
        $name=$_POST['name'];
          
            if (empty($name)) {
                redirect("appendDevice.php?msg=empty");
            }

            if (strlen($name) > 12) {
                redirect("appendDevice.php?msg=deviceLength");
            }

           
            $url = "WEB_ADDRESS/api/add_device?";
        $data = array(
            "name" => $name
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
        if ($result["Status"] == "SUCCESS") {
            redirect("index.php?msg=DeviceAdded");
        } else {

            if ($result["MSG"] == "Device already exists") {
                redirect("appendDevice.php?msg=DeviceExists");
            } else {
                redirect("appendDevice.php?msg=unknown");
            }

        }

    }

?>