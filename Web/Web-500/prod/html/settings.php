<?php
//make sure they're logged in
session_start();
if(!isset($_SESSION['id']))
{
	header('Location: /');
	die();
}

require_once("functions.php");
$conn = connect_to_db();
$error = "";

$userInfo = get_info($conn, $_SESSION['id']);
if(gettype($userInfo) == "string")
	$error = "There was an error retrieving information about your profile";
else
{
	$username = htmlentities($userInfo['username']);
	$pubkey = htmlentities($userInfo['pubkey']);
}

if(isset($_POST['pubkey']) && isset($_POST['pin']))
{
	$fail = false;
	//don't allow target user's public key to be reset by douche bags
	if($_SESSION['id'] === $targetID)
	{
		$fail = true;
		$error = "Trying to change Julian Assange's private key eh? BOO, NOT COOL. DISQUALIFIED.\n<br><br>\n";
		$error .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/9oOATRG0hCw" frameborder="0" allowfullscreen></iframe>';
		log_failed_pubkey_update($conn, $_SESSION['id'], $_SERVER);
	}
	else
		$valid = validate_user($conn, $_SESSION['id'], $userInfo['username'], $_POST['pin']);

	if(!$error && $valid)
	{
		if(trim($_POST['pubkey']) == "")
			$error = "public key cannot be empty";
		else
		{
			$result = update_user($conn, $_SESSION['id'], $_POST['pubkey']);
			if($result)
				$error = "There was an error updating your information.";
			else
				$pubkey = htmlentities($_POST['pubkey']);
		}
	}
	else
	{
		sleep(2);
		if(!$fail)
			$error .= "Invalid PIN.";
	}
}

$conn->close();

$loggedIn = true;
$clientIp = getClientIp();
$activePage = "settings";
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion msg-container">
					<div class="grid_3 grid_5 wow fadeInUp animated" data-wow-delay=".5s">
					<?php
					if($error)
					{
						echo <<<EOF
						<div class="well" style="color: red;">
							$error
						</div>
EOF;
					}
					if(true)
					{
						echo <<<EOF
						<div id="msg-header-container">
							<h3 class="hdg" style="margin-bottom: 0;">$username</h3>
						</div>
						<form action="settings.php" method="POST">
							<p id="pin-para" class="wow fadeInUp animated">PIN</p>
							<input type="password" class="form-control msg-subject" name="pin" maxlength="64" placeholder="PIN">
							<p id="dec-msg-para" class="wow fadeInUp animated">Private Key</p>
							<textarea placeholder="Enter PGP private key" name="pubkey" class="btn1" id="pubkey">$pubkey</textarea>
							<button class="btn1" style="margin-top: 10px;">Update</button>
						</form>
					</div>
EOF;
					}
					?>
				</div>
				</div>
<?php
require_once("footer.php");
?>
