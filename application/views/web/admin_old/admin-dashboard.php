<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<?=$this->seo->output();?>
		
		<link rel="stylesheet" href="<?=base_url('assets/css/admin/layout.css')?>" type="text/css" media="screen" />
		<!--[if lt IE 9]>
		<link rel="stylesheet" href="<?=base_url('assets/css/admin/ie.css')?>" type="text/css" media="screen" />
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<?php 
		foreach($css_files as $file): ?>
			<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
		<?php endforeach; ?>
		<?php foreach($js_files as $file): ?>
			<script src="<?php echo $file; ?>"></script>
		<?php endforeach; ?>
	</head>
	<body>
	
		<header id="header">
		<hgroup>
			<h1 class="site_title">
				<a href="<?=site_url('/admin')?>">Website Admin</a>
				<img id="logo" src="<?=base_url('assets/img/logo.png')?>" alt="Logo">
			</h1>
			<h2 class="section_title"><?=$title?></h2>
			<div class="btn_view_site">
				<a href="<?=site_url()?>">View Site</a>
			</div>
		</hgroup>
	</header> <!-- end of header bar -->
	
	<section id="secondary_bar">
		<div class="user">
			<p><?=get_human_name() ?></p>
			<!-- <a class="logout_user" href="#" title="Logout">Logout</a> -->
		</div>
		<div class="breadcrumbs_container">
			<article class="breadcrumbs">
				<a href="<?=site_url('/admin')?>">Website Admin</a>
				<? foreach ($breadcrumbs as $key=>$value):?>
					<div class="breadcrumb_divider"></div>
					<a href="<?=$value['link']?>" class="<?=($key == count($breadcrumbs)) ? 'current' : ''?>"><?=$value['title']?></a>
				<? endforeach; ?>
			</article>
		</div>
	</section><!-- end of secondary bar -->
	
	<aside id="sidebar" class="column">
		<h3>Content</h3>
		
		<ul class="toggle">
			<li class="icn_tags"><a href='<?php echo site_url('admin/dashboard/film_management')?>'>Films</a></li>
			<li class="icn_tags"><a href='<?php echo site_url('admin/dashboard/film_management_twitter_bootstrap')?>'>Twitter Bootstrap Theme [BETA]</a></li>
		</ul>
		<hr/>
		<h3>Users</h3>
		<ul class="toggle">
			<li class="icn_add_user"><a href="#">Add New User</a></li>
			<li class="icn_view_users"><a href="#">View Users</a></li>
			<li class="icn_profile"><a href="#">Your Profile</a></li>
		</ul>
		<hr/>
		<h3>Media</h3>
		<ul class="toggle">
			<li class="icn_folder"><a href="#">File Manager</a></li>
			<li class="icn_photo"><a href="#">Gallery</a></li>
			<li class="icn_audio"><a href="#">Audio</a></li>
			<li class="icn_video"><a href="#">Video</a></li>
		</ul>
		<hr/>
		<h3>Admin</h3>
		<ul class="toggle">
			<li class="icn_settings"><a href="#">Options</a></li>
			<li class="icn_security"><a href="#">Security</a></li>
			<li class="icn_jump_back"><a href="<?=site_url('/admin/dashboard/logout')?>">Logout</a></li>
		</ul>
		
		<footer>
			<hr />
			<p><strong>Copyright &copy; <?=date("Y")?> The Website Admins</strong></p>
			<p>Theme by <a href="http://www.medialoot.com">MediaLoot</a></p>
		</footer>
	</aside><!-- end of sidebar -->
	
	<section id="main" class="column">
		
		<h4 class="alert_info">Welcome to admin panel, this could be an informative message.</h4>
		
		<article class="module main-content">
				<?php echo $output; ?>
				
		</article><!-- end of content manager article -->
		

		
		<div class="clear"></div>

		<div class="spacer"></div>
	</section> 

	</body>
</html>
