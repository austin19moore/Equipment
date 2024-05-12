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

                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                         {
                              echo '<div class="alert alert-danger" role="alert">Serial number cannot be empty!</div>';

                         }
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="exists")
                         {
                              echo '<div class="alert alert-danger" role="alert">Serial number does not exist!</div>';

                         }
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                         {
                              echo '<div class="alert alert-danger" role="alert">Unknown error has occured, please try again!</div>';
                              die();
                         }
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unresponsive")
                        {
                            echo '<div class="alert alert-danger" role="alert">Server did not respond!</div>';
                        }
                   ?>
                    <h3>Find equipment to modify: </h3>
                    <br>
                    <form method="POST" action="">
                    <div class="form-group">
                        <label for="exampleSerial">Serial Number:</label>
                        <input type="text" class="form-control" id="serialInput" name="serialnumber">
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit" value="submit">Modify Equipment</button>
                   </form>
                    

               </div>
          </div>
     </section>
</body>
</html>

<?php
    if (isset($_POST['submit']))
    {
        $serialNumber=trim($_POST['serialnumber']);

        if (empty($serialNumber)) {
            redirect("modifySearch.php?msg=empty");
        }

        // post data to url using curl
        $url = "WEB_ADDRESS/api/view_equipment?";
        $data = array(
            "serialnumber" => $serialNumber
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

        curl_close($ch);
        $result = curl_exec($ch);
        $equipment = json_decode($result, true);
        curl_close($ch);

        if ($equipment['Status'] == NULL) {
          redirect("index.php?msg=unresponsive");
        }

        if ($equipment['MSG'] == "serial number does not exist" || $equipment['MSG'] == "No equipment found") {
          redirect("modifySearch.php?msg=exists");
          }

        if ($equipment['Status'] == "ERROR") {
          redirect("modifySearch.php?msg=unknown");
        }

        redirect('modify.php?serialnumber='.$serialNumber);
    }
?>