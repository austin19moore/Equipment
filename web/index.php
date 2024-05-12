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
                    <a href="#" class="navbar-brand">Equipment Database</a>
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
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="EquipmentAdded")
                         {
                             echo '<div class="alert alert-success" role="alert">Equipment successfully added!</div>';
 
                         }
                         if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceAdded")
                        {
                            echo '<div class="alert alert-success" role="alert">Device successfully added!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ManufacturerAdded")
                        {
                            echo '<div class="alert alert-success" role="alert">Manufacturer successfully added!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ModifySuccess")
                        {
                            echo '<div class="alert alert-success" role="alert">Equipment successfully modified!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="DeviceModified")
                        {
                            echo '<div class="alert alert-success" role="alert">Device successfully modified!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="ManufacturerModified")
                        {
                            echo '<div class="alert alert-success" role="alert">Manufacturer successfully modified!</div>';

                        }
                        if (isset($_REQUEST['msg']) && $_REQUEST['msg']=="unresponsive")
                        {
                            echo '<div class="alert alert-danger" role="alert">Server did not respond!</div>';
                        }
                    ?>

                    <div class="col-md-4 col-sm-4">
                         <div class="feature-thumb">
                              <h3>Search Equipment</h3>
                              <p>Click here to search equipment.</p>
                              <a href="search.php" class="btn btn-default smoothScroll">Discover more</a>
                         </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                         <div class="feature-thumb">
                              <h3>Add Equipment</h3>
                              <p>Click here to add new equipment</p>
                             <a href="add.php" class="btn btn-default smoothScroll">Discover more</a>
                         </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                         <div class="feature-thumb">
                              <h3>Modify Equipment</h3>
                              <p>Click here to modify existing equipment</p>
                             <a href="modify.php" class="btn btn-default smoothScroll">Discover more</a>
                         </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                         <div class="feature-thumb">
                              <h3>Modify Devices</h3>
                              <p>Click here to modify existing devices</p>
                             <a href="modifyDevice.php" class="btn btn-default smoothScroll">Discover more</a>
                         </div>
                    </div>
                    <div class="col-md-4 col-sm-4">
                         <div class="feature-thumb">
                              <h3>Modify Manufacturers</h3>
                              <p>Click here to modify existing manufacturers</p>
                             <a href="modifyManufacturer.php" class="btn btn-default smoothScroll">Discover more</a>
                         </div>
                    </div>
               </div>
          </div>
     </section>
</body>
</html>