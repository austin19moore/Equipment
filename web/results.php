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
               <!-- MENU LINKS -->
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

               <?php 
                    include("../functions.php");

                    $device=$_GET['device'];
                    $manufacturer=$_GET['manufacturer'];
                    $serialNumber=$_GET['serialNumber'];
                    $active=$_GET['active'];


                    if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="NoneFound")
                    {
                         echo '<div class="alert alert-danger" role="alert">No equipment found!</div>';
                         echo '<button class="btn btn-primary"><a href="search.php" style="color:white">Back</a></button>';
                         die();

                    }
                    if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unknown")
                    {
                         echo '<div class="alert alert-danger" role="alert">An unknown error has occured, please try again!</div>';
                         echo '<button class="btn btn-primary"><a href="search.php" style="color:white">Back</a></button>';
                         die();
                    }

                 // get equipment

                 $url = "WEB_ADDRESS/api/list_equipment?";
                 $data = array(
                     "device" => $device,
                     "manufacturer" => $manufacturer,
                     "serialnumber" => $serialNumber,
                     "allowinactive" => $active
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
                    redirect("search.php?msg=unresponsive");
                }
                 if ($result['MSG'] == "No equipment found") {
                    redirect("results.php?msg=NoneFound");
                 }
                 if ($result["Status"] == "ERROR") {

                    // redirect("results.php?msg=unknown");
                    echo ''.$equipment['MSG'];
                    die();
                }
         
                 $data = $result["Data"];
         
                 curl_close($ch);
     
                
               ?>
                    <button class="btn btn-primary"><a href="search.php" style="color:white">Back</a></button>
                    <label for="list">Results (LIMIT 1000):</label>
                    <ul class="list-group">
                         <?php
                              if ($active == 1) {
                                   echo '<h5>Allowing inactive</h5>';
                              }

                              // results
                              foreach ($data as $value) {
                                echo '<a href="view.php?serialnumber='.$value['serialnumber'].'"><li class="list-group-item">Device: '.$value['device'].', Manufacturer: '.$value['manufacturer'].', Serial number: '.$value['serialnumber'].'</li>';
                              }
                         ?>
                    </ul>
               </div>
          </div>
     </section>
</body>
</html>