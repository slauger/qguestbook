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
* @subpackage functions
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK'))
{
	die("Hacking attempt");
	exit;
}

function generate_sid()
{
	return md5((time()/2)*time());
}

function get_sid()
{
	$session_id = (isset($_SESSION['session_id'])) ? $_SESSION['session_id'] : false;
	return $session_id;
}

function get_user_id()
{
	$user_id = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : false;
	return $user_id;
}

function user_exists($name, $pass)
{
	global $db;
	$sql = 'SELECT user_id
		FROM ' . USERS_TABLE . '
			WHERE user_name = ' .  $db->sql_escape($name) . ' AND user_pass = ' .  $db->sql_escape(md5($pass)) . '
		LIMIT 1';
	$result = $db->sql_query($sql);
	if ($db->sql_numrows($result))
	{
		while ($row = $db->sql_fetchrow($result))
		{
			return $row['user_id'];
		}
	}
	else
	{
		return false;
	}
}

function user_login($user_id, $session)
{
	global $db, $user_ip;
	$_SESSION['user_id'] = $user_id;
	$_SESSION['session_id'] = $session;
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET user_session = ' . $db->sql_escape($session) . ', user_ip = ' . $db->sql_escape($user_ip) . ', user_time = ' . $db->sql_escape(time()) . '
			WHERE user_id = ' . $db->sql_escape($user_id) . '
		LIMIT 1';
	$db->sql_query($sql);
}

function sessions_expired()
{
	global $db;
	$sql = 'SELECT user_time, user_id
		FROM ' . USERS_TABLE . '
			WHERE user_session <> ' . $db->sql_escape('');

	$result = $db->sql_query($sql);
	$expired = array();

	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['user_time'] < (time() - 10 * 60))
		{
			$expired[] = $row['user_id'];
		}
	}
	return $expired;
}

function user_update()
{
	global $db;
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET user_time = ' . $db->sql_escape(time()) . '
			WHERE user_id = ' . $db->sql_escape(get_user_id()) . '
		LIMIT 1';
	$result = $db->sql_query($sql);
}

function user_logged_in()
{
	global $db;

	if (!get_sid())
	{
		return false;
	}

	$sql = 'SELECT user_id
		FROM ' . USERS_TABLE . '
			WHERE user_session = ' . $db->sql_escape(get_sid()) . '
		LIMIT 1';

	$result = $db->sql_query($sql);
	if ($row = $db->sql_numrows($result))
	{
		return true;
	}
}

function user_logout()
{
	global $db;
	session_destroy();
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET user_session = ' . $db->sql_escape('') . '
			WHERE user_session = ' . $db->sql_escape(get_sid()) . '
		LIMIT 1';
	$db->sql_query($sql);
}

function session_clean($user_id)
{
	global $db;
	$sql = 'UPDATE ' . USERS_TABLE . '
		SET user_session = ' . $db->sql_escape('') . '
			WHERE user_id = ' . $db->sql_escape($user_id) . '
		LIMIT 1';
	$db->sql_query($sql);
}

function get_auth_level($user_id)
{
	global $db;
	$sql = 'SELECT user_level
		FROM ' . USERS_TABLE . '
		WHERE user_id = ' . $db->sql_escape($user_id) . '
		LIMIT 1';
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	return $row['user_level'];
}

?>