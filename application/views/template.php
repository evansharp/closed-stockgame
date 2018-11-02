<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> - Stock Market Game</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php echo base_url();?>css/libs/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
	<link rel="stylesheet" href="<?php echo base_url();?>css/libs/chartist-plugin-tooltip.min.css">

	<link rel="stylesheet" href="<?php echo base_url();?>css/game_main.css?v=<?php echo time();?>">
	<link rel="stylesheet" href="<?php echo base_url();?>css/topbar.css?v=<?php echo time();?>">
</head>

<body>

	<div id="layout">
    <nav id="topbar">
      	<ul id="tabs">
	        <li><a href="ticker" class="<?php if( $active_nav == "ticker" ){ echo "active"; } ?>"> Tickers </a></li>
	        <li><a href="prospectus" class="<?php if( $active_nav == "prospectus" ){ echo "active"; } ?>"> Prospectus </a></li>
	        <li><a href="buysell" class="<?php if( $active_nav == "buysell" ){ echo "active"; } ?>"> Buy / Sell </a></li>
	        <li><a href="portfolio" class="<?php if( $active_nav == "portfolio" ){ echo "active"; } ?>"> My Portfolio </a></li>
	        <li><a href="history" class="<?php if( $active_nav == "history" ){ echo "active"; } ?>"> History </a></li>
			<li><a href="leaderboard" class="<?php if( $active_nav == "leaderboard" ){ echo "active"; } ?>"> Leaderboard </a></li>
    	</ul>



    	<?php if( $logged_in ): ?>
				<img src="<?php echo $userData['picture']; ?>" alt="" class="user_img">
				<a href="logout" id="logout_button">Logout</a>
    	<?php else: ?>
    		<a href="<?php echo $login_url; ?>" id="login_button">Login</a>
    	<?php endif; ?>

    </nav>

    <div id="main">

			<?php
				if ( $logged_in ){

					echo $page;

				}elseif ( !$logged_in ){

					echo '<aside id="not_logged_in">';
					echo '<p><i class="fa fa-4x fa-ban" aria-hidden="true"></i></p>';
					echo '<p>You must be logged-in to play.</p>';
					echo '</aside>';

				}
					//echo '<pre>';
					//var_dump($userData);
					//echo '<hr>';
					//var_dump($classData);
					//echo '</pre>';

				if($is_admin){
					echo "<a id='admin_link' class='pure-button pure-button-primary' href='admin'>ADMIN</a>";
				}?>
		</div> <!-- end #main -->
	</div> <!-- end #layout -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script src="<?php echo base_url(); ?>js/chartist-plugin-tooltip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/moment.min.js"></script>

		<script src="<?php echo base_url(); ?>js/main.js?<?php echo time();?>"></script>
		<script src="<?php echo base_url(); ?>js/tickers.js?<?php //echo time();?>"></script>
		<script src="<?php echo base_url(); ?>js/portfolio.js?<?php //echo time();?>"></script>
    </body>

</html>
