<?php
/**
* qGuestbook
*
* An advanced Guestbook written in PHP5.
*
* PHP version 5
*
* LICENSE:
* Copyright (C) 2007-2008 Simon Lauger
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* @category   Guestbook
* @package    qGuestbook
* @subpackage common
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: auth.php 53 2008-04-01 19:42:59Z kwhark $
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
    die("Hacking attempt");
    exit;
}

// Steuerklasse
// Für gemeinsame Methoden
Class Authentication
{
	// für update_user();
	private $this->user_id;
	
	public function user_exists($username, $password)
	{
		global $db;
		if (empty($password)) {
			$where_password = '';
		} else {
			$password = md5($password);
			$where_password = 'AND user_pass = ' . $db->sql_escape($password);
		}
		
		$sql = 'SELECT COUNT(´users_id´)
			FROM ' . POSTS_TABLE . '
				WHERE user_name = ' . $db->sql_escape($username) . '
				' . $where_password . '
			LIMIT 1';
		$result = $db->sql_query($sql);
		
		if (!$db->sql_result($result, 0)) {
			return false;
		} else {
			return true;
		}
	}
	
	// Datum in der DB Updaten
	public function update_user($mode) {}
	
	public function unique_id()
	{
		return md5(time()/2*time());
	}
}

// Apache HTTP Based Authentication
Class Auth_Apache2 Extends Authentication
{	
	public function __construct()
	{
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
			$this->authenticate();
		}

		if (!$this->user_exists($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
			$this->authenticate();
		} else {
			$this->user_id = '';
		}
		
		if ($this->last_action() < (time() - 10 * 60)) {
			$this->authenticate();
		} else {
			$this->update_user();
		}
	}
	
	public function authenticate()
	{
		header("WWW-Authenticate: Basic realm=\"Administrativer Bereich\"");
		header("HTTP/1.0 401 Unauthorized");
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0//EN\">
			<html>
				<head>
					<title>Zugriff verweigert!</title>
					<meta http-equiv=\"refresh\" content=\"0; URL=.../\">
            			</head>
            			<body>
            				<h2>Zugriff verweigert!</h2>
            			</body>
            		</html>";
		exit;
	}
		
	public function __destruct()
	{
		// $this->update_user('delete');
		$this->authenticate();
	}
}

// Sessionbasierter Login
Class Auth_Sessions Extends Authentication
{
	public function __construct() {}
	
	public function authenticate() {}
	
	public function __destruct()
	{
		$this->
	}
	
}

// Cookies - kommen irgendwann mal
Class Auth_Cookies Extends Authentication
{
	
}

?>