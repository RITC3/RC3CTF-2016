<?php

$salt = "what a great salt amirite?";
$pinSalt = 'w0wzer$ even better salttttttt';
$target = "julianassange";
$targetID = 3;
$targetIp = "54.172.225.153";

function connect_to_db($find = 0){
	if($find == 0)
		return new mysqli("localhost", "dba", "YupThisIsDaNewDBP4\$\$", "cachet");
	else
		return new mysqli("localhost", "find-dba", "YupThisIsDaFindP4\$\$", "cachet");

}

function register_user($conn, $username, $password, $pin, $pubkey, $admin = 0){
	//setup prepared statement
	$statement = $conn->prepare("INSERT INTO users (username, password, pin, pubkey, admin) VALUES (?, ?, ?, ?, ?)");

	//generate hash
	global $salt;
    $pass = hash("sha512", $salt . $username . $password);

	//generate PIN hash
	global $pinSalt;
	$pin = hash("sha512", $pinSalt . $username . $pin);

	//bind params to prepared statement, execute query
	$statement->bind_param("ssssi", $username, $pass, $pin, $pubkey, $admin);
	$statement->execute();
    if($statement->error)
        $result = $statement->error."<br />".$sql;
    else
        $result = $statement->insert_id;

	$statement->close();
    return $result;
}

function check_user($conn, $username){
	$statement = $conn->prepare("SELECT id, username FROM users WHERE username = ?");
	$statement->bind_param("s", $username);
	$statement->execute();

    $result = $statement->get_result();
    echo $statement->error;

    if($result->num_rows > 0)
        return True; 
    else
        return False;
}

function login_user($conn, $username, $ptpass){
	global $salt;
    $password = hash("sha512", $salt . $username . $ptpass);
	$statement = $conn->prepare("SELECT id,username FROM users WHERE username = ? AND password = ?");
	$statement->bind_param("ss", $username, $password);
	$statement->execute();
	$query = $statement->get_result();
    $error = $statement->error;

	if($error)
        $result = "a";

    if ($query->num_rows > 0)
	{
		$result = $query->fetch_array(MYSQLI_ASSOC);
		$query->free();
	}
    else
        $result = "a";

	//get whether login succeeded or not
	$success = 0;
	if($result && gettype($result) != "string")
		$success = 1;
	
	log_failed_login($conn, $username, $ptpass, $success);

	return $result;
}

function validate_user($conn, $id, $username, $pin){
	global $pinSalt;
    $hashedPin = hash("sha512", $pinSalt . $username . $pin);
	$statement = $conn->prepare("SELECT id,username FROM users WHERE id = ? AND pin = ?");
	$statement->bind_param("is", $id, $hashedPin);
	$statement->execute();
	$query = $statement->get_result();
    $error = $statement->error;

	if($error)
        $result = false;

    if ($query->num_rows > 0)
		$result = true;
    else
        $result = false;

    $query->free();
	return $result;
}

function get_info($conn, $id){
	if($id === "")
	{
		$result['error'] = "ID must not be blank.";
		return $result;
	}

	if(is_numeric($id))
	{
		$statement = $conn->prepare("SELECT * FROM users where id = ?");
		$statement->bind_param("i", $id);
	}
	else
	{
		$statement = $conn->prepare("SELECT * FROM users where username = ?");
		$statement->bind_param("s", $id);
	}

	$statement->execute();
	$query = $statement->get_result();
	$error = $statement->error;

	if($error)
		return "There was an error";
	else
	{
		$result = $query->fetch_array(MYSQLI_ASSOC);
		$query->free();
		if(count($result) == 0)
			$result = "There was an error";

		return $result;
	}
}

function lookup_user($conn, $id){
    if(is_numeric($id))
	{
		$statement = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
		$statement->bind_param("i", $id);
	}
    else
	{
		$statement = $conn->prepare("SELECT id, username FROM users WHERE "
									. "username LIKE ?");
		$id = "%" . $id . "%";
		$statement->bind_param("s", $id);
	}

	$statement->execute();
	$error = $statement->error;
	if($error)
		return "There was an error.";

	$result = $statement->get_result();
	$statement->close();

	return $result;
}

function get_messages($conn, $userid, $read=0, $display=1)
{
	$statement = $conn->prepare("select m.id, message, subject, fromID, username, timesent FROM messages m JOIN users u on u.id = m.fromID WHERE toID = ? AND msg_read = ? AND display = ?");
	$statement->bind_param("iii", $userid, $read, $display);
	$statement->execute();

	$error = $statement->error;
	$result = $statement->get_result();
	$statement->close();

	if($error)
		return $error;
	else
		return $result;
	#return array($result, $error);
}

function get_message($conn, $userid, $id, $display=1)
{
	global $targetIp;
	$clientIp = getClientIp($smart=0);
	if($clientIp == $targetIp)
		$display = 0;

	$statement = $conn->prepare("select m.id, message, subject, fromID, toID, msg_read, username, timesent FROM messages m JOIN users u on u.id = m.fromID WHERE m.id = ? AND toID = ? AND display = ?");
	$statement->bind_param("iii", $id, $userid, $display);
	$statement->execute();

	$error = $statement->error;
	$result = $statement->get_result();
	$statement->close();

	if($error)
		return $error;
	else
	{
		#if no result was returned, generate an error
		if($result->num_rows == 0)
			$result = "no result";
		else //if result returned, mark message as read
		{
			$result = $result->fetch_assoc();
			if($result['toID'] == $userid && !$result['msg_read'])
				mark_read($conn, $id);
		}

		return $result;
	}
}

function send_message($conn, $msg, $toID, $fromID, $subject)
{
	global $targetID;

	//if being sent to target make display=0 so our script will read it
	if($toID == $targetID)
	{
		$display = 0;
		$statement = $conn->prepare("INSERT INTO messages (message, toID, fromID, subject, display) VALUES (?, ?, ?, ?, ?)");
		$statement->bind_param("siisi", $msg, $toID, $fromID, $subject, $display);
	}
	else
	{
		$statement = $conn->prepare("INSERT INTO messages (message, toID, fromID, subject) VALUES (?, ?, ?, ?)");
		$statement->bind_param("siis", $msg, $toID, $fromID, $subject);
	}
	$statement->execute();

	$error = $statement->error;
	$result = $statement->insert_id;
	$statement->close();

	if($error)
		return $error;
	else
		return $result;
}

function mark_read($conn, $id)
{
	$statement = $conn->prepare("UPDATE messages SET msg_read = 1 WHERE id = ?");
	$statement->bind_param("i", $id);
	$statement->execute();
	$statement->close();
}

function update_user($conn, $id, $pubkey)
{
	$statement = $conn->prepare("UPDATE users SET pubkey = ? WHERE id = ?");
	$statement->bind_param("si", $pubkey, $id);
	$statement->execute();

	$error = $statement->error;
	$statement->close();

	return $error;
}

function log_failed_login($conn, $username, $password, $success)
{
	if($success)
		$password = "";

	$ip = getClientIp();
	$port = $_SERVER['REMOTE_PORT'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$cookies = $_SERVER["HTTP_COOKIE"];
	$statement = $conn->prepare("INSERT INTO logins (success, username, password, remote_ip, remote_port, user_agent, cookies) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$statement->bind_param("issssss", $success, $username, $password, $ip, $port, $user_agent, $cookies);
	$statement->execute();

	$error = $statement->error;
	$result = $statement->insert_id;
	$statement->close();

	if($error)
		return $error;
	else
		return $result;
}

function log_failed_pubkey_update($conn, $id, $all)
{
	$ip = getClientIp();
	$port = $_SERVER['REMOTE_PORT'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$cookies = $_SERVER["HTTP_COOKIE"];
	$post_vars = json_encode($_POST);
	$get_vars = json_encode($_GET);
	$statement = $conn->prepare("INSERT INTO pubkey_fails (userid, remote_ip, remote_port, user_agent, cookies, post_vars, get_vars) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$statement->bind_param("issssss", $id, $ip, $port, $user_agent, $cookies, $post_vars, $get_vars);
	$statement->execute();

	$error = $statement->error;
	$result = $statement->insert_id;
	$statement->close();

	if($error)
		return $error;
	else
		return $result;
}

function starts_with($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

//this code is what ran during the competition. this function was not meant to be bypassed but it can be.
//this gist is a modification that prevents most bypasses: https://gist.github.com/bigshebang/c12fa853cf2eae7e255c51f71735856c
//using history.pushState() should be able to circumvent any referer check (with xss of course)
//itszn (RPISEC), valis (Dragon Sector) and sasdf (not sure what team) found bypasses for this check
//there may have been others but that's all i remember/know about
function referer_good($id)
{
	//if id isn't numeric can't be valid
	if(!is_numeric($id))
		return false;

	$referer = "";
	if(!empty($_SERVER['HTTP_REFERER']))
		$referer = strtolower($_SERVER['HTTP_REFERER']);
	else
		return false;

	//$server = $_SERVER["SERVER_ADDR"]; //gives the private ip
	$server = "54.172.225.153";
	$good_referer = "/messages.php?id=" . $id;

	//remove the server part from the referer
	$pos = strpos($referer, $server) + strlen($server);
	$referer = substr($referer, $pos);
	if(starts_with($referer, $good_referer))
		return true;
	else
		return false;
}

function getClientIp($smart=1) {
    $ipaddress = '';
	if(!$smart)
		$ipaddress = $_SERVER['REMOTE_ADDR'];
    if(array_key_exists('HTTP_CLIENT_IP', $_SERVER) && $_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(array_key_exists('HTTP_X_FORWARDED', $_SERVER) && $_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(array_key_exists('HTTP_FORWARDED_FOR', $_SERVER) && $_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(array_key_exists('HTTP_FORWARDED', $_SERVER) && $_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(array_key_exists('REMOTE_ADDR', $_SERVER) && $_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

?>
