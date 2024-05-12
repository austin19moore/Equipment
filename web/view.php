<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>CCJ314</title>
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

                    <a href="#" class="navbar-brand">View Equipment Database</a>
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

                        
                        if ($serial == NULL && !isset($_REQUEST['msg'])) {
                            redirect("view.php?msg=empty");
                            die();
                        }

                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="empty")
                         {
                              echo '<div class="alert alert-danger" role="alert">No equipment found!</div>';
                            die();
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
                        $url = "WEB_ADDRESS/api/view_equipment?";
                        $data = array(
                            "serialnumber" => $serial
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
                        $result = curl_exec($ch);
                        $equipment = json_decode($result, true);
                        if ($equipment['Status'] == NULL) {
                         redirect("index.php?msg=unresponsive");
                        }
                        if ($equipment['MSG'] == "No equipment found") {
                         redirect("view.php?empty");
                         }
                         if ($equipment['MSG'] == "ERROR") {
                              redirect("view.php?unknown");
                         }
                        $equipment = $equipment["Data"];

                        
                        curl_close($ch);
                        echo '<h3>Device name:</h3>';
                        echo '<p>'.$equipment["device"].'</p>';
                        echo '<h3>Manufacturer name:</h3>';
                        echo '<p>'.$equipment['manufacturer'].'</p>';
                        echo '<h3>Active:</h3>';
                        if ($equipment['active'] == "0") {
                         echo '<p>False</p>';
                        } else {
                         echo '<p>True</p>';
                        }
                        echo '<h3>Serial Number</h3>';
                        echo '<p>'.$equipment['serialnumber'].'</p><br>';
                        echo '<a href="modify.php?serialnumber='.$equipment['serialnumber'].'"><button class="btn btn-primary">Modify Equipment</button></a>';
                   ?>

               </div>
          </div>
     </section>
</body>
</html>