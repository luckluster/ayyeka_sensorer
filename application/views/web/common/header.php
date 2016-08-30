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
	
	<!-- jquery ui CSS -->
	<link rel="stylesheet" href="<?=base_url('assets/vendor/jquery-ui-1.12.0/jquery-ui.css')?>" />

    <!-- Custom styles for this template -->
    <link href="<?=base_url('assets/css/starter-template.css')?>" rel="stylesheet">
	
	<!-- script material. Placed on top for simplicity -->
    <script src="<?=base_url('assets/vendor/jquery-3.1.0.js')?>"></script>
	<script src="<?=base_url('assets/vendor/jquery-ui-1.12.0/jquery-ui.js')?>"></script>
	<script src="<?=base_url('assets/vendor/bootstrap/js/bootstrap.js')?>"></script>
	
	<!-- grid -->
	<link href="<?=base_url('assets/orca_grid/orca_grid.css')?>" rel="stylesheet">
	<script src="<?=base_url('assets/orca_grid/orca_grid.js')?>"></script>
	
	<!-- Global JS script -->
	<script src="<?=base_url('assets/js/script.js')?>"></script>
	
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
          <a class="navbar-brand" href="<?=site_url()?>">Ayyeka Sensorer</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
			<!-- use 'class="active"' for marking currently active page -->
			<?php if (get_user_id()) { ?>
				<li><a href="<?=site_url()?>">Home</a></li>
				<li><a href="<?=site_url("/reports")?>">Reports</a></li>
				<li><a href="<?=site_url("/machines")?>">My machines</a></li>
				<li><a href="<?=site_url("/input")?>">Data input</a></li>
				<li><a href="<?=site_url("/main/logout")?>">Logout <?=get_user_prop('usr_name')?></a></li>
			<?php } else { ?>
				<li><a href="<?=site_url()?>">Login</a></li>
			<?php } ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
	
    
		