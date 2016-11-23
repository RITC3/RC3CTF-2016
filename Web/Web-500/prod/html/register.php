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
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['repassword']) && isset($_POST['pin']) && isset($_POST['pubkey']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	$repassword = $_POST['repassword'];
	$pin = $_POST['pin'];
	$pubkey = $_POST['pubkey'];

	if($username == "") //blank user name
        $error = "Username must not be blank.";
    else if(strlen($username) > 64)
        $error = "Username cannot be longer than 64 characters.";
    else if(check_user($conn, strtolower($username))) //check if user exists
        $error = "That username already exists.";
    else if($password == "")
        $error = "Password must not be blank.";
    else if($password != $repassword)
        $error = "Passwords do not match.";
    else if($pin == "")
        $error = "PIN must not be blank.";
    else if($pubkey == "")
        $error = "Public key must not be blank.";

	if(!$error)
    {
        $name = strtolower($username);
        $result = register_user($conn, $name, $password, $pin, $pubkey);
        session_start();
        $_SESSION['id'] = $result;
        $_SESSION['username'] = $name;
		$conn->close();
        header("Location: unread.php");
        die();
    }

$conn->close();
}

$loggedIn = false;
$clientIp = getClientIp();
$activePage = "register";
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion" style="text-align:center;">
					<h3 class="hdg wow fadeInUp animated" data-wow-delay=".5s">Sign up</h3>
					<?php
					if($error)
					{
						echo "<div style=\"margin-bottom: 30px; margin-top: 1.5em;\">\n";
						echo "<p style=\"color: red;\" class=\"error\">" . htmlentities($error) . "</p>\n";
						echo "</div>\n";
					}
					?>
					<form action="register.php" method="POST">
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="text" class="form-control" name="username" maxlength="64" placeholder="Username">
						</div>
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="password" name="password" class="form-control" placeholder="Password">
						</div>
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="password" name="repassword" class="form-control" placeholder="Confirm Password">
						</div>
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="password" name="pin" class="form-control" placeholder="PIN">
						</div>
						<div class="input-group">
							<textarea placeholder="Public PGP key" name="pubkey"></textarea>
						</div>
						<button class="btn1">Register</button>
					</form>
				</div>
				</div>
<?php
require_once("footer.php");
?>
