<html>
	<head>
		<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro|Open+Sans+Condensed:300|Raleway' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/game_main.css">

	</head>
	<body>
		<div id="main">
			<div id="envelope">
				<?php if (isset($authUrl)){ ?>
				<header id="sign_in">
					<h2>Login</h2>
				</header>
				<hr>
				<div id="content">
					<center>
						<a href="<?php echo $authUrl; ?>"><img src="<?php echo base_url(); ?>image/googlesignin.png" ></a>
						</center>
				</div>
				<?php }else{ ?>
				
				<header id="info">
					<a target="_blank" class="user_name" href="<?php echo $userData->link; ?>" />
						<img class="user_img" src="<?php echo $userData->picture; ?>" width="15%" />
						<?php echo '<p class="welcome"><i>Welcome ! </i>' . $userData->name . "</p>"; ?></a>
						<a class='logout' href='https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=<?php echo base_url(); ?>logout'>Logout</a>
				</header>
				<?php
				echo "<p class='profile'>Profile :-</p>";
				echo "<p><b> First Name : </b>" . $userData->given_name . "</p>";
				echo "<p><b> Last Name : </b>" . $userData->family_name . "</p>";
				echo "<p><b> Gender : </b>" . $userData->gender . "</p>";
				echo "<p><b> Email : </b>" . $userData->email . "</p>";
				?>
				<?php }?>
			</div>
		</div>
	</body>
</html>