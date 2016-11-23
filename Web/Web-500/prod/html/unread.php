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

$conn = connect_to_db();
/*if(isset($_POST['message_id']))
{
	
}*/

$loggedIn = true;
$clientIp = getClientIp();
$activePage = "unread";
require_once("header.php");
?>
				<div class="codes">
				<div class="container opinion" style="padding-bottom: 50px;">
					<h3 class="hdg wow fadeInUp animated" data-wow-delay=".5s" style="text-align: center;">Unread Messages</h3>
					<!--<p class="wow fadeInUp animated" data-wow-delay=".5s">Enable a hover state on table rows within a <code>&lt;tbody&gt;</code>.</p>-->
					<div class="bs-docs-example wow fadeInUp animated" data-wow-delay=".5s">
						<table class="table table-hover">
							<thead>
								<tr>
								  <th>#</th>
								  <th>Username</th>
								  <th>Subject</th>
								  <th>&nbsp;</th>
								  <th>Sent</th>
								</tr>
							</thead>
							<tbody>
							<?php
							$messages = get_messages($conn, $_SESSION['id'], $read=0);

							if(gettype($messages) == "string")
							{
								$error = true;
								echo <<<EOF
								<tr>
								  <td>1</td>
								  <td colspan=4 style="color: red;">There was an error retrieving your messages, please try again later</td>
								</tr>
EOF;
							}
							else
							{
								$i = 0;
							}

							if(!$error && $messages->num_rows == 0) // no messages
							{
								echo <<<EOF
								<tr>
								  <td>1</td>
								  <td colspan=4>Looks like there aren't any messages here!</td>
								</tr>
EOF;
							}


							while(!$error && ($msg = $messages->fetch_assoc()))
							{
								$i++;
								$id = $msg['id'];
								$from = htmlentities($msg['username']);
								$subject = htmlentities($msg['subject']);
								$datetime = htmlentities($msg['timesent']);
								echo <<<EOF
								<tr style="cursor:pointer;" onclick="location.href='messages.php?id=$id'">
								  <td>$i</td>
								  <td>$from</td>
								  <td colspan=2>$subject</td>
								  <td>$datetime</td>
								</tr>
EOF;
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				</div>
<?php
$conn->close();
require_once("footer.php");
?>
