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
$result = "";

//handle sent message
if(isset($_POST['encMessage']) && isset($_POST['toID']) && isset($_POST['subject']))
{
	if($_POST['encMessage'] == "")
		$error = "Must send the encrypted message.";
	else if($_POST['toID'] == "")
		$error = "Must send the id of the recipient";
	else if(trim($_POST['subject'] == ""))
		$error = "Must send the subject of the message. This isn't email! You can't just send stuff without a subject. People like you are the reason why we can't have nice things.";
	
	if(!$error)
	{
		$subject = substr(trim($_POST['subject']), 0, 64);
		$result = send_message($conn, $_POST['encMessage'], $_POST['toID'], $_SESSION['id'], $subject);
		if(gettype($result) == "string")
			$error = "There was an error sending the message to the recipient. Please try again later.";
		else
		{
			$origEncMsg = $_POST['encMessage'];
			//$dbResult = get_message($conn, $_SESSION['id'], $result);
			//$dbEncMsg = $dbResult['message'];
			$dbEncMsg = "";
			/*echo <<<EOF
			<p id="origEncMsg">$origEncMsg</p>
			<!--<p id="dbEncMsg">$dbEncMsg</p>-->
EOF;*/
		}
	}
}

$loggedIn = true;
$clientIp = getClientIp();
$activePage = "send";
$customScripts = array("kbpgp-2.0.8-min.js", "encrypt.js");
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion msg-container">
				<div class="grid_3 grid_5 wow fadeInUp animated" data-wow-delay=".5s">
				<?php
				if($error)
				{
					echo "<div style=\"margin-bottom: 30px; margin-top: 1.5em;\">\n";
					echo "<p style=\"color: red;\" class=\"error\">" . htmlentities($error) . "</p>\n";
					echo "</div>\n";
				}
				else if(isset($_GET['id']))
				{
					if(trim($_GET['id']) == "")
						$error = "There was an error retrieving information about the requested user.";
					if(!$error)
					{
						$userInfo = get_info($conn, $_GET['id']);
						if(gettype($userInfo) == "string")
							$error = "There was an error retrieving information about the requested user.";
					}
					
					if($error)
					{
						echo "<div style=\"margin-bottom: 30px; margin-top: 1.5em;\">\n";
						echo "<p style=\"color: red;\" class=\"error\">" . htmlentities($error) . "</p>\n";
						echo "</div>\n";
					}
					else{
						$toID = $userInfo['id'];
						$pubkey = htmlentities($userInfo['pubkey']);
						$username = htmlentities($userInfo['username']);
						echo <<<EOF
					<h3 class="hdg wow fadeInUp animated" style="text-align: center;" data-wow-delay=".5s">Send a Message</h3>
						<p style="display:none" id="dest-pubkey">$pubkey</p>
						<p id="encP"></p>
						<p id="enc-msg-para" class="wow fadeInUp animated">To:</p>
						<div id="username" class="well">
							$username
						</div>

					<form action="send.php" id="message-form" method="POST">
						<div>
						<input type="hidden" name="encMessage" id="encMessage">
						<input type="hidden" name="toID" value="$toID">
						</div>
						<input type="text" class="form-control" id="msg-subject" name="subject" maxlength="64" placeholder="Subject">
						<textarea id="origMessage" placeholder="Message (we will encrypt this in your browser before sending to our servers)" name="origMessage"></textarea>
					</form>
					<button class="btn1" onclick="encryptAndSend()">Send</button>
EOF;
					}
				}
				else //they need to search for a user
				{
					echo <<<EOF
					<div style="text-align: center;">
					<h3 class="hdg wow fadeInUp animated" data-wow-delay=".5s">Find Another User</h3>

					<form action="send.php" method="GET">
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="text" class="form-control" name="id" maxlength="64" placeholder="Username">
						</div>
						<button class="btn1">Search</button>
					</form>
					</div>
EOF;
				}
				echo <<<EOF
				</div>
				</div>
				</div>
EOF;

$conn->close();
require_once("footer.php");
?>
