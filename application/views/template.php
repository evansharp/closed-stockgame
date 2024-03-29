<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?> - Stock Market Game</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" crossorigin="anonymous">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js" integrity="sha512-zO8oeHCxetPn1Hd9PdDleg5Tw1bAaP0YmNvPY8CwcRyUk7d7/+nyElmFrB6f7vg4f7Fv4sui1mcep8RIEShczg==" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />

	<link rel="stylesheet" href="<?php echo base_url();?>css/game_main.css?v=<?php echo time();?>">
	<link rel="stylesheet" href="<?php echo base_url();?>css/topbar.css?v=<?php echo time();?>">
	<link rel="stylesheet" href="<?php echo base_url();?>css/admin.css?v=<?php echo time();?>">

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.css">
</head>

<body class="<?php echo $active_nav;?>">

	<div id="layout">
	    <nav id="topbar">
	      	<ul id="tabs">
		        <li><a href="<?php echo base_url(); ?>ticker" class="<?php if( $active_nav == "ticker" ){ echo "active"; } ?>"> Tickers </a></li>
		        <li><a href="<?php echo base_url(); ?>prospectus" class="<?php if( $active_nav == "prospectus" ){ echo "active"; } ?>"> Prospectus </a></li>
		        <li><a href="<?php echo base_url(); ?>buysell" class="<?php if( $active_nav == "buysell" ){ echo "active"; } ?>"> Buy / Sell </a></li>
		        <li><a href="<?php echo base_url(); ?>portfolio" class="<?php if( $active_nav == "portfolio" ){ echo "active"; } ?>"> My Portfolio </a></li>
		        <li><a href="<?php echo base_url(); ?>history" class="<?php if( $active_nav == "history" ){ echo "active"; } ?>"> History </a></li>
	    	</ul>

		 	<?php if( isset($_SESSION['user']['user_role']) ): ?>
				<?php if(isset($_SESSION['user']['google_avatar'])):?>
					<img src="<?php echo $_SESSION['user']['google_avatar']; ?>" alt="" class="user_img">
				<?php endif;?>
				<a href="<?php echo base_url();?>logout" id="logout_button">Logout</a>
	    	<?php else: ?>
	    		<a href="<?php echo $login_url; ?>" id="login_button">Login</a>
	    	<?php endif; ?>
	    </nav>

		<div id="main">

			<?php
				$splash = '<div id="title_splash">
				<p><i class="fa fa-4x fa-chart-line" aria-hidden="true"></i></p>
				<h1> The Stockmarket Game </h1>
				</div>';

				if( $game_online === true || isset( $_SESSION['user']['user_role'] ) && $_SESSION['user']['user_role'] >= USER_ROLE_ADMIN ){
					// game is online or user is admin

					if ( isset( $_SESSION['user']['user_role'] ) ){
						// user had not fallen-through auth as 'unauthorized'

						echo $page;



						if( $game_online === false ){
							//reminder tag for ME
							echo '<aside id="game_not_online_reminder">GAME OFFLINE</aside>';
						}

					}
				}else{
					// game is offline and user is not an admin
					echo $splash;

					echo '<aside id="game_offline">';
					echo '<p><i class="fa fa-4x fa-info-circle" aria-hidden="true"></i></p>';
					echo '<p>The game is currently offline.</p>';
					echo '</aside>';

				}


					if(isset( $_SESSION['user']['user_role'] ) && $_SESSION['user']['user_role'] >= USER_ROLE_ADMIN  && $title != 'Admin'){
					echo "<a id='admin_link' class='' href='admin/dashboard'>ADMIN</a>";
				}?>
			</div> <!-- end #main -->
		</div> <!-- end #layout -->

	    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" crossorigin="anonymous"></script>
	  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" crossorigin="anonymous"></script>

	    <script src="https://kit.fontawesome.com/f0db4e53ea.js" crossorigin="anonymous" SameSite="None"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js" integrity="sha512-SuxO9djzjML6b9w9/I07IWnLnQhgyYVSpHZx0JV97kGBfTIsUYlWflyuW4ypnvhBrslz1yJ3R+S14fdCWmSmSA==" crossorigin="anonymous"></script>

		<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@0.7.7"></script>
		<!-- Import D3 for auto colour gen -->
		<script src="https://d3js.org/d3-color.v1.min.js"></script>
		<script src="https://d3js.org/d3-interpolate.v1.min.js"></script>
		<script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/moment.min.js"></script>

		<script> var base_url = "<?php echo base_url(); ?>"; </script>
		<script src="<?php echo base_url(); ?>js/main.js?v=<?php echo time();?>"></script>

			<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" SameSite="None"></script>
			<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js" SameSite="None"></script>

			<script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.js" SameSite="None"></script>
			<script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js" SameSite="None"></script>

			<script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.js" SameSite="None"></script>
			<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.js" SameSite="None"></script>
			<script src="<?php echo base_url(); ?>js/dataTables.editor.free.min.js"></script>
			<script src="<?php echo base_url(); ?>js/colour-generator.js"></script>
			<script src="<?php echo base_url(); ?>js/tickers.js?v=<?php echo time();?>"></script>
			<script src="<?php echo base_url(); ?>js/portfolio.js?v=<?php echo time();?>"></script>
			<script src="<?php echo base_url(); ?>js/admin.js?v=<?php echo time();?>"></script>
	</body>

</html>
