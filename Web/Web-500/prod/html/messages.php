<?php
//make sure they're logged in
session_start();
if(!isset($_SESSION['id']))
{
	header('Location: /');
	die();
}

require_once("functions.php");
$error = "";

if(!isset($_GET['id']))
{
	header('Location: /unread.php');
	die();
}

$loggedIn = true;
$activePage = "";
$clientIp = getClientIp();
$customScripts = array("kbpgp-2.0.8-min.js", "decrypt.js");
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion msg-container">
					<div class="grid_3 grid_5 wow fadeInUp animated" data-wow-delay=".5s">
					<?php
					$havePin = false;
					//GET and POST params are nasty, but can't put PIN in get or they could get it via JavaScript
					if(isset($_GET['id']) && !isset($_POST['pin']))
					{
						$id = intval($_GET['id']);
						//get pin and post back to this page
						echo <<<EOF
						<div id="msg-header-container">
							<h3 class="hdg" style="margin-bottom: 0;">Enter PIN to Read Message</h3>
						<form action="messages.php?id=$id" method="POST">
							<p id="pin-para" class="wow fadeInUp animated">Required to ensure the privacy of your message</p>
							<input type="password" class="form-control msg-subject" id="pin" name="pin" maxlength="64" placeholder="PIN">
							<button class="btn1" id="pin-button" style="margin-top: 10px;">Submit</button>
						</form>
						</div>
					</div>
EOF;
					}
					else if(isset($_GET['id']) && isset($_POST['pin']))
					{
						$valid = false;
						$good_referer = referer_good($_GET['id']);
						if($good_referer)
						{
							$conn = connect_to_db();
							$valid = validate_user($conn, $_SESSION['id'], $_SESSION['username'], $_POST['pin']);
						}

						if($valid)
						{
							$havePin = true;
							$message = get_message($conn, $_SESSION['id'], $_GET['id']);
							$conn->close();
							if(gettype($message) == "string")
								$error = "There was an error retrieving the requested message. If you keep getting this error, you probably can't read the requested message.";
							else
							{
								$username = htmlentities($message['username']);
								$subject = htmlentities($message['subject']);
								$timestamp = $message['timesent'];

								//don't XSS the admin
								if($_SESSION['id'] === 1)
									$encMsg = htmlentities($message['message']);
								else
									$encMsg = $message['message'];
							}
						}
						else
						{
							if($conn)
								$conn->close();

							$error = "Invalid PIN, try again.";
						}


					}

					if($error)
					{
						echo <<<EOF
						<div class="well" style="color: red;">
							$error
						</div>
EOF;
					}
					else if($havePin)
					{
						echo <<<EOF
						<div id="msg-header-container">
							<h3 class="hdg" style="margin-bottom: 0;">$username - $subject</h3>
							<p class="wow fadeInUp animated">$timestamp</p>
						</div>
						<p id="enc-msg-para" class="wow fadeInUp animated">Encrypted Message</p>
						<div id="enc-msg" class="well">
							$encMsg
						</div>
						<br>
						<p id="dec-msg-para" class="wow fadeInUp animated">Decrypted Message</p>
						<div id="dec-msg" class="well">
							<i>Enter your private key below and press 'Decrypt' to populate the decrypted message here!</i>
						</div>
						<p id="passphrase-para" class="wow fadeInUp animated">Passphrase</p>
						<input type="password" class="form-control input_class" id="msg-subject" name="subject" maxlength="64" placeholder="Enter passphrase for PGP key">
						<p id="privkey-para" class="wow fadeInUp animated">Private Key</p>
						<textarea placeholder="Enter PGP private key" class="btn1" id="privkey"></textarea>
						<button class="btn1" id="decrypt-button" style="margin-top: 10px;" onclick="decryptMessage()">Decrypt</button>
EOF;
					}
					?>
					</div>
				</div>
				</div>
<?php
require_once("footer.php");
?>
