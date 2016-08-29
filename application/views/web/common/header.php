<?
// Header for the Sensorer app. YEa
// Using jquery and bootstrap.
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title><?=arr_get_value($params, 'title')?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=base_url('assets/vendor/bootstrap/css/bootstrap.css')?>" rel="stylesheet" />


    <!-- Custom styles for this template -->
    <link href="starter-template.css" rel="stylesheet">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">


    <title><?=arr_get_value($params, 'title')?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=base_url('assets/vendor/bootstrap/css/bootstrap.css')?>" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?=base_url('assets/css/starter-template.css')?>" rel="stylesheet">
	
	<!-- script material. Placed on top for simplicity -->

    <script src="<?=base_url('assets/js/jquery-3.1.0.js')?>"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
	<script src="<?=base_url('assets/vendor/bootstrap/js/bootstrap.js')?>"></script>
	
	<!-- grid -->
	<link href="<?=base_url('assets/orca_grid/orca_grid.css')?>" rel="stylesheet">
	<script src="<?=base_url('assets/orca_grid/orca_grid.js')?>"></script>
	
	
	<script>
		$(document).ready(function() {
			Orca_grid.init();
		});
	</script>
  </head>

  <body>



    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Ayyeka Sensorer</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	
    
		