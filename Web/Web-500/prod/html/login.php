<?php
//make sure they're not already logged in
session_start();
if(isset($_SESSION['id']))
{
	header('Location: unread.php');
	die();
}

#kill session we started for the login check above
session_unset();
session_destroy();

require_once("functions.php");
$error = "";

$conn = connect_to_db();
if(isset($_POST['username']) && isset($_POST['password']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];

	if($username == "") //blank user name
        $error = "Username must not be blank.";
    else if($password == "")
        $error = "Password must not be blank.";

	if(!$error)
    {
        $name = strtolower($username);
        $login = login_user($conn, $name, $password);
		if($login)
		{
			if(gettype($login) != "string")
			{
				session_start();
        		$_SESSION['id'] = $login['id'];
        		$_SESSION['username'] = $login['username'];
				$conn->close();
        		header("Location: unread.php");
        		die();
			}
		}
		$error = "Invalid username or password";
    }

$conn->close();
}

$loggedIn = false;
$clientIp = getClientIp();
$activePage = "login";
require_once("header.php");
?>
				<div class="codes" style="padding-bottom:200px;">
				<div class="container opinion" style="text-align:center;">
					<h3 class="hdg wow fadeInUp animated" data-wow-delay=".5s">Login</h3>
					<?php
					if($error)
					{
						echo "<div style=\"margin-bottom: 30px; margin-top: 1.5em;\">\n";
						echo "<p style=\"color: red;\" class=\"error\">" . htmlentities($error) . "</p>\n";
						echo "</div>\n";
					}
					?>
					<form action="login.php" method="POST">
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="text" class="form-control" name="username" id="username-field" maxlength="64" placeholder="Username">
						</div>
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="password" name="password" id="password-field" class="form-control" placeholder="Password">
						</div>
						<button id="login-button" class="btn1">Login</button>
					</form>
				</div>
				</div>
<?php
require_once("footer.php");
?>
