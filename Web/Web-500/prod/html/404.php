<?php
session_start();
$loggedIn = false;
if(isset($_SESSION['id']))
{
	$loggedIn = true;
}

$activePage = "";
$title = "404 Not Found!!! | Cachet";
require_once("header.php");
?>
	<!-- services -->
	<div class="services">
		<div class="container">
			<div class="services-info">
				<h3>Oops! Couldn't find that for you..</h3>
				<p style="margin-bottom: 2em;">Maybe you'll have better luck finding what you're looking for on interdimensional cable!</p>
				<a id="register-link" href="http://inter-dimensionalcable.xyz/">Try it!</a>
			</div>
		</div>
	</div>
	<!-- //services -->
<?php
require_once("footer.php");
?>
