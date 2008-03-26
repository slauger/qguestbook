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
* @subpackage admin
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    0.2.4
* @link       http://www.simlau.net/
*/

//
// Konstanten
//
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 3);

//
// Dateien includieren
//
$root_dir = '../';
include_once $root_dir . "includes/common.php";

page_header('Benutzer Administration');

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'delete':
		if (!isset($_GET['id']) || empty($_GET['id']))
		{
			message_die('Kein Benutzer ausgewählt', 'Du musst einen Benutzer auswählen, den du löschen willst.');
		}

		$sql = 'DELETE FROM ' . USERS_TABLE . '
			WHERE user_id = ' . $db->sql_escape($_GET['id']) . '
			LIMIT 1';
		$db->sql_query($sql);

		message_die('Benutzer wurde gelöscht', 'Der gewählte Benutzer wurde erfolgreich gelöscht');
	break;
	case 'edit':
		message_die($lang['guestbook_error'], 'Dieses Feature existiert noch nicht, bitte benutzen sie dazu phpMyAdmin oder ähnliche Tools');

		if (!isset($_GET['id']) || empty($_GET['id']))
		{
			message_die($lang['guestbook_error'], 'Du musst einen Benutzer auswählen, den du bearbeiten willst.');
		}

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . $db->sql_escape($_GET['id']);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		$template->set_filenames(array(
			'body' => 'users_update_body.html',
		));

		$template->assign_vars(array(
			'ID' => '',
			'NAME' => '',
			'EMAIL' => '',
		));

		$template->pparse('body');
	break;
	case 'add':
		if (!isset($_POST['username']) || !$user = valdiate_username($_POST['username']))
		{
			message_die($lang['guestbook_error'], 'Es wurde ein Feld (username) nicht ausgefüllt!');
		}

		if (!isset($_POST['email']) || !$email = valdiate_email($_POST['email']))
		{
			message_die($lang['guestbook_error'], 'Es wurde ein Feld (email) nicht ausgefüllt!');
		}

		if (!isset($_POST['password']) || !$pass = valdiate_password($_POST['password']))
		{
			message_die($lang['guestbook_error'], 'Es wurde ein Feld (pass) nicht ausgefüllt!');
		}

		$auth_level = (isset($_POST['auth_level']) && !empty($_POST['auth_level'])) ? $_POST['auth_level'] : USER_ANONYMUS;

		$sql = 'INSERT INTO ' . USERS_TABLE . '
			( `user_id` , `user_name` , `user_pass` , `user_email` , `user_session` , `user_time` , `user_ip` , `user_level` ) VALUES (' . $db->sql_escape('') . ', ' . $db->sql_escape($user) . ', ' . $db->sql_escape($pass) . ' , ' . $db->sql_escape($email) . ', ' . $db->sql_escape('') . ', ' . $db->sql_escape('') . ', ' . $db->sql_escape('') . ', ' . $db->sql_escape($auth_level) . ')';
		if (!$db->sql_query($sql))
		{
			message_die('SQL Error', 'Es gab nen Fehler und so!');
		}

		message_die('Benutzer wurde erfolgreich angelegt', sprintf('Der Benutzer %s wurde erfolgreich angelegt!', $user));

	break;
	default:
		$template->set_filenames(array(
			'index' => 'users_body.html',
		));

		$template->assign_vars(array(
			'USER_REGISTRED' => USER_REGISTRED,
			'USER_MODERATOR' => USER_MODERATOR,
			'USER_ADMINISTRATOR' => USER_ADMINISTRATOR,
		));

		$sql = 'SELECT user_id, user_name, user_email, user_session, user_level
			FROM ' . USERS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$user_online = (empty($row['user_session'])) ? 'Offline' : 'Online';
			switch ($row['user_level'])
			{
				case USER_REGISTRED:
					$user_level = 'Registrierter Benutzer';
				break;
				case USER_MODERATOR:
					$user_level = 'Moderator';
				break;
				case USER_ADMINISTRATOR:
					$user_level = 'Administrator';
				break;
				default:
					$user_level = 'Gast';
				break;
			}
			$template->assign_block_vars('users', array(
				'ID' => decode_html($row['user_id']),
				'USERNAME' => decode_html($row['user_name']),
				'EMAIL' => decode_html($row['user_email']),
				'ONLINE' => decode_html($user_online),
				'LEVEL' => $user_level,
			));
		}

		$template->pparse('index');
}

include_once $root_dir . 'includes/footer.php';

?>
