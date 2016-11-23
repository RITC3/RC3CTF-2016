<!DOCTYPE html>
<html>
<head>
<?php
if(empty($title))
	$title = "Cachet: The world's oldest and most trusted messaging platform";

echo "<title>$title</title>";
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- bootstrap-css -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<!--// bootstrap-css -->
<!-- css -->
<link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
<!--// css -->
<link rel="stylesheet" href="css/owl.carousel.css" type="text/css" media="all">
<link href="css/owl.theme.css" rel="stylesheet">
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
<!-- font -->
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700italic,700,400italic,300italic,300' rel='stylesheet' type='text/css'>
<!-- //font -->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/SmoothScroll.min.js"></script>
<?php
if(!empty($customScripts))
{
	foreach($customScripts as $script)
	{
		echo "<script src=\"js/" . $script . "\"></script>\n";
	}
}
?>
</head>
<body>
	<!-- banner -->
	<div class="banner">
		<!--header-->
		<div class="header">
			<div class="logo">
				<h1><a href="/">Cachet</a></h1>
			</div>
			<div class="top-nav">
			<?php
			if(!empty($clientIp) && $clientIp == $targetIp)
				$menu_img = "";
			else
				$menu_img = "images/menu.png";

			echo <<<EOF
				<span class="menu"><img src="$menu_img" alt="CACHET"/></span>
EOF;
			?>
				<ul>
					<?php
					$activeClass = "class=\"active\"";
					if($loggedIn) //logged in menu
					{
						$findClass = "";
						$sendClass = "";
						$readClass = "";
						$unreadClass = "";
						$settingsClass = "";
						if($activePage != "none")
						{
							if($activePage == "find")
								$findClass = $activeClass;
							else if($activePage == "send")
								$sendClass = $activeClass;
							else if($activePage == "read")
								$readClass = $activeClass;
							else if($activePage == "unread")
								$unreadClass = $activeClass;
							else if($activePage == "settings")
								$settingsClass = $activeClass;
						}
						echo <<<EOF
						<li><a $readClass href="read.php">Read</a></li>
						<li><a $unreadClass href="unread.php">Unread</a></li>
						<li><a $sendClass href="send.php">Send</a></li>
						<li><a $findClass href="find.php">Search</a></li>
						<li><a $settingsClass href="settings.php">Settings</a></li>
						<li><a href="logout.php">Logout</a></li>
EOF;
					}
					else //public menu
					{
						$homeClass = "";
						$aboutClass = "";
						$loginClass = "";
						$registerClass = "";
						if($activePage != "none")
						{
							if($activePage == "home")
								$homeClass = $activeClass;
							else if($activePage == "about")
								$aboutClass = $activeClass;
							else if($activePage == "login")
								$loginClass = $activeClass;
							else if($activePage == "register")
								$registerClass = $activeClass;
						}
						echo <<<EOF
						<li><a $homeClass href="/">Home</a></li>
						<li><a $aboutClass href="about.php">About</a></li>
						<li><a href="#">&nbsp;</a></li>
						<li><a href="#">&nbsp;</a></li>
						<li><a $loginClass href="login.php">Login</a></li>
						<li><a $registerClass href="register.php">Sign up</a></li>
EOF;
					}
					?>

				</ul>
				<!-- script-for-menu -->
				<script>					
					$("span.menu").click(function(){
						$(".top-nav ul").slideToggle("slow" , function(){
						});
					});
				</script>
				<!-- script-for-menu -->
			</div>
				<div class="clearfix"> </div>
		</div>	
	</div>
	<!-- //banner -->
