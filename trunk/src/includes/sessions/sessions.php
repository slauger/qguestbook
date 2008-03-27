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
* @subpackage sessions
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

// Workaround! :D
// Damit auch alle Seiten schön kompatibel bleiben...
if (!defined('REQUIRED_AUTH_LEVEL')) {
	if (defined('ADMIN_PAGE')) {
		define('REQUIRED_AUTH_LEVEL', USER_ADMINISTRATOR);
	} else {
		define('REQUIRED_AUTH_LEVEL', 0);
	}
}

session_start();

if ($sessions_expired = sessions_expired()) {
	foreach ($sessions_expired as $session_expired) {
		if (get_user_id() == $session_expired) {
			$user_session_expired = true;
		} else {
			session_clean($session_expired);
		}
	}
}

if (isset($user_session_expired) && $user_session_expired == true) {
	user_logout();
	message_die('Deine Session ist abgelaufen', 'Du musst dich jetzt erneut einloggen.<br /><br />Klicke <a href="login.php">hier</a> um zur Login Seite zu gelangen<br /><br />Klicke <a href="../index.php">hier</a> um zum Gästebuch zurückzukehren.');
}

user_update();
$user_id = get_user_id();
$user_auth_level = get_auth_level($user_id);
$user_session_id = get_sid();

// Dank diesem kleinen Hack wäre es sogar möglich,
// die komplette Moderation für "Gäste" freizuschalten und als
// Auth-System nur noch .htaccess zu verwenden. ;)
if (REQUIRED_AUTH_LEVEL > USER_ANONYMOUS) {
	if (!user_logged_in($user_session_id)) {
		header('Location: ' . PAGE_ADMIN_LOGIN);
		exit;
	}
	if (!($user_auth_level >= REQUIRED_AUTH_LEVEL)) {
		message_die('Zugriff nicht erlaubt', 'Du hast nicht die benötigten Rechte...');
	}
}

?>