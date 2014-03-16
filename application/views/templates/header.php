<!doctype html>
<html lang="en">
  
	<head>
		<title><?php echo $page_title; ?></title>

		<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">    

		<?php echo link_tag('css/bootstrap.min.css'); ?>

		<?php echo link_tag('css/base-admin.css'); ?>

		<?php echo link_tag('css/jquery.qtip.min.css'); ?>

		<?php echo link_tag('css/style.css'); ?>

		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">

		<?php echo link_tag('font-awesome-4.0.3/css/font-awesome.min.css'); ?>
		
	</head>

<body>

<div class="wrapper"> <!-- sticky footer -->

<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">

			<a class="brand" href="<?php echo base_url(); ?>"><i class="fa fa-bar-chart-o"></i> DFS NBA Tools</a>	

		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->



<div class="subnavbar">

	<div class="subnavbar-inner">
	
		<div class="container">

			<ul class="mainnav">
				
				<li class="<?php echo ($page_type === 'Daily DS' ? 'active' : ''); ?>">
					<a href="<?php echo base_url(); ?>">
						<i class="fa fa-dashboard"></i>
						<span>Daily DS</span>
					</a>	    				
				</li>

				<li class="<?php echo ($page_type === 'Daily FD' ? 'active' : ''); ?>">
					<a href="<?php echo base_url().'daily/fd'; ?>">
						<i class="fa fa-dashboard"></i>
						<span>Daily FD</span>
					</a>	    				
				</li>

				<li class="<?php echo ($page_type === 'Players' ? 'active' : ''); ?>">
					<a href="<?php echo base_url().'players'; ?>">
						<i class="fa fa-users"></i>
						<span>Players</span>
					</a>	    				
				</li>

				<li class="<?php echo ($page_type === 'Teams' ? 'active' : ''); ?>">
					<a href="<?php echo base_url().'teams'; ?>">
						<i class="fa fa-tasks"></i>
						<span>Teams</span>
					</a>	    				
				</li>

				<li class="<?php echo ($page_type === 'Search' ? 'active' : ''); ?>">
					<a href="<?php echo base_url().'search'; ?>">
						<i class="fa fa-search"></i>
						<span>Search</span>
					</a>	    				
				</li>
		
				<li class="<?php echo ($page_type === 'Update' ? 'active' : ''); ?>">
					<a href="<?php echo base_url().'update'; ?>">
						<i class="fa fa-upload"></i>
						<span>Update</span>
					</a> 				
				</li>

			</ul>

		</div> <!-- /container -->
	
	</div> <!-- /subnavbar-inner -->

</div> <!-- /subnavbar -->