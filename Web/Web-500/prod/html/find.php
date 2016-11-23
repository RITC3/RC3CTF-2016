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

//handle sent message
/*if(isset($_POST['id']))
{
	if($_POST['encMessage'] == "")
		$error = "Must send the encrypted message.";
	else if($_POST['toID'] == "")
		$error = "Must send the id of the recipient";
	else if(trim($_POST['subject'] == ""))
		$error = "Must send the subject of the message. This isn't email! You can't just send stuff without a subject. People like you are the reason why we can't have nice things.";
	
	if(!$error)
	{
		$subject = substr(trim($_POST['subject'],0,64));
		$result = send_message($conn, $_POST['encMessage'], $_POST['toID'], $_SESSION['id'], $subject);
		if(gettype($result) == "string")
			$error = "There was an error sending the message to the recipient. Please try again later.";
	}
}*/

$loggedIn = true;
$activePage = "find";
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion msg-container">
				<div class="grid_3 grid_5 wow fadeInUp animated" data-wow-delay=".5s">
				<?php
				if(isset($_GET['id']))
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
					else
					{
						$id = $userInfo['id'];
						$username = htmlentities($userInfo['username']);
						$pubkey = htmlentities($userInfo['pubkey']);
						echo <<<EOF
							<h3 class="hdg wow fadeInUp animated" style="text-align: center;" data-wow-delay=".5s">$username</h3>
							<p id="enc-msg-para" class="wow fadeInUp animated">Public Key</p>
							<div id="pubkey" class="well">
								$pubkey
							</div>
							<button class="btn1" style="margin-top: 10px;" onclick="location.href='send.php?id=$id'">Send a Message</button>
						</div>
EOF;
					}
				}
				else if(isset($_GET['searchid']))
				{
					if(trim($_GET['searchid']) == "")
						$error = "There was an error retrieving information about the requested user.";
					if(!$error)
					{
						$users = lookup_user($conn, $_GET['searchid']);
						if(gettype($users) == "string")
							$error = "There was an error retrieving information about the requested user.";
					}

					$conn->close();

					echo <<<EOF
						<h3 class="hdg wow fadeInUp animated" style="text-align: center;" data-wow-delay=".5s">Results</h3>
						<table class="table table-hover">
							<thead>
								<tr>
								  <th>#</th>
								  <th>Username</th>
								</tr>
							</thead>
							<tbody>
EOF;
					if($error)
					{
						$error = htmlentities($error);
						echo <<<EOF
                            <tr>
                              <td>&nbsp;</td>
                              <td>$error</td>
                            </tr>
EOF;
					}
					else{
						while($user = $users->fetch_assoc())
                        {
                            $i++;
                            $id = $user['id'];
                            $username = htmlentities($user['username']);
                            echo <<<EOF
                            <tr style="cursor:pointer;" onclick="location.href='find.php?id=$id'">
                              <td>$i</td>
                              <td>$username</td>
                            </tr>
EOF;
                        }
					}

					echo <<<EOF
                            </tbody>
                        </table>
EOF;
				}
				else //they need to search for a user
				{
					echo <<<EOF
					<div style="text-align: center;">
					<h3 class="hdg wow fadeInUp animated" data-wow-delay=".5s">Find Another User</h3>

					<form action="find.php" method="GET">
						<div class="input-group wow fadeInUp animated" data-wow-delay=".5s">
							<input type="text" class="form-control" name="searchid" maxlength="64" placeholder="Username">
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

require_once("footer.php");
?>
