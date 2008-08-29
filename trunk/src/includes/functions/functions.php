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

if (!defined('GUESTBOOK')) {
    die("Hacking attempt");
    exit;
}

function site_microtime()
{
	$microtime = explode(' ', microtime());
	$microtime = $microtime[0] + $microtime[1];
	return $microtime;
}

function br2nl($string)
{
	return str_replace("<br />", "\n", $string);
}

function icq_url($icq)
{
	global $encode;
	if (!is_int($icq))
	{
		$icq = $encode->encode_html($icq);
	}
	$url = sprintf("http://www.icq.com/people/about_me.php?uin=%1s", $icq);
	return $url;
}

function user_color($userlevel)
{
	switch ($userlevel)
	{
		case USER_ANONYMOUS:
			return COLOR_ANONYMOUS;
		break;
		case USER_REGISTRED:
			return COLOR_REGISTRED;
		break;
		case USER_MODERATOR:
			return COLOR_MODERATOR;
		break;
		case USER_ADMINISTRATOR:
			return COLOR_ADMINISTRATOR;
		break;
		default:
			return COLOR_ANONYMOUS;
		break;
	}
}

function site_microtime_calc($begin, $end)
{
	$microtime = round($end - $begin, 3);
	return $microtime;
}

function encode_ip($dotquad_ip)
{
	$ip_sep = explode('.', $dotquad_ip);
	return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

function decode_ip($int_ip)
{
	$hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
	return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

function message_die($title, $message)
{
	global $root_dir, $template, $db;
	global $microtime, $config_table, $lang;

	if (!defined('HEADER_INC')) {
		include_once $root_dir . 'includes/header.php';
	}

	$template->set_filenames(array(
		'error'=> 'error_body.html'
	));

	if (defined('ADMIN_PAGE')) {
		$template->assign_block_vars('page_info_on', array());
		$template->assign_vars(array(
			'L_PAGE_TITLE' => $title,
			'L_PAGE_DESC' => 'Bitte beachte die Nachricht in der untenstehenden Box.',
		));
	}
	else {
		$template->assign_block_vars('page_info_off', array());
	}

	$template->assign_vars(array(
		'ERROR_TITLE' => $title,
		'ERROR_MESSAGE' => $message,
	));

	$template->pparse('error');

	include_once $root_dir . 'includes/footer.php';
}

// Wird nicht mehr benutzt!
function generate_link($mode, $param = array())
{
	switch ($mode) {
		case 'return_login':
		
		break;
		case 'return_guestbook':
			#sprintf($lang['click_return_guestbook'], '<a>', '</a>');
		break;
		case 'go_back':
			$return = sprintf("<a href=\"javascript:history.back()\" title=\"%s\">%s</a>", $param['title'], $param['text']);
			return $return;
		break;
		case 'default':
			$return =  sprintf("<a href=\"%s\" title=\"%s\">%s</a>", $param['href'], $param['title'], $param['text']);
			return $return;
		break;
	}
	return false;
}

function format_date($timestamp = '')
{
	global $config_table;
	$timestamp = (empty($timestamp)) ? time() : $timestamp;
	$formated = date($config_table['default_dateformat'], $timestamp);
	return $formated;
}

// Dummer Funktionsname in 0.2.0
function encode_html($string)
{
	return decode_html($string);
}

// Dummer Funktionsname in 0.2.0 II
function decode_html($string)
{
	global $encode;
	return $encode->encode_html($string);
}

function update_config_table($field, $value)
{
	global $db, $config_table;
	if (isset($value)) {
		$sql = 'UPDATE ' . CONFIG_TABLE . '
			SET config_value = ' . $db->sql_escape($value) . '
			WHERE config_name = ' . $db->sql_escape($field) . '
			LIMIT 1';
		$result = $db->sql_query($sql);
		return true;
	}
	return false;
}

function generate_quote($post_id)
{
	global $db;
	$sql = 'SELECT posts_name, posts_text
		FROM ' . POSTS_TABLE . '
			WHERE posts_id = ' . $db->sql_escape($post_id) . '
		LIMIT 1';
	$result = $db->sql_query($sql);

	// Fixed in 0.2.4
	if (!$db->sql_numrows($result)) {
		return "";
	}
	
	while ($row = $db->sql_fetchrow($result)) {
		$quote_text = sprintf("[quote=%s]%s[/quote]\n", $row['posts_name'], $row['posts_text']);
	}

	return $quote_text;
}

// Wird nicht mehr verwandt!
// Bitte das Modul BBCode verwenden
function bbcode($string)
{
	global $bbcode, $config_table;
	$string = nl2br(words_cut(badwords(($string))));
	$string = ($config_table['smilies']) ? smilies($string) : $string;
	$string = ($config_table['bbcode']) ? $bbcode->parse($string) : $string;
	return $string;
}

function real_path()
{
	global $config;
	$url = (!$config->get('https')) ? 'http://' : 'https://';
	$url .= $_SERVER['HTTP_HOST'] . '/';
	$url .= $config->get('script_path');
	return $url;
}

function banned_email($user_email)
{
	global $db;
	$sql = 'SELECT banlist_email
		FROM ' . BANLIST_TABLE . '
		WHERE banlist_email = ' . $db->sql_escape($user_email);
	$result = $db->sql_query($sql);

	if (!$db->sql_numrows($result)) {
		return false;
	}
	return true;
}

function banned_ip($user_ip)
{
	global $db;
	$sql = 'SELECT banlist_ip
		FROM ' . BANLIST_TABLE . '
		WHERE banlist_ip = ' . $db->sql_escape($user_ip);
	$result = $db->sql_query($sql);

	if (!$db->sql_numrows($result))
	{
		return false;
	}
	return true;
}

/**
  * function valdiate_error
  * @param $error Fehler ID
  * @since 0.2.4
  */
function valdiate_error($error)
{
	switch ($error)
	{
		case 'name':
			return 'Bitte gib deinen Namen zu deinem Gästebucheintrag an!';
		case 'email':
			return 'Bitte gib eine valide E-Mail Adresse zu deinem Gästebucheintrag an!';
		case 'www':
			return 'Deine angegebene URL zu deiner Webseite enstspricht nicht den allgemeinen Standarts!';
		case 'textarea':
			return 'Bitte gib einen Text zu deinem Gästebucheintrag an!';
		case 'icq':
			return 'Deine angegebene ICQ UIN ensrpicht nicht den allgemeinen Standarts!';
		case 'flood':
			return 'Du kannst nicht so schnell hintereinander Beiträge schreiben!';
		case 'confirmation':
			return 'Du hast die Spamschutz-Frage nicht beantwortet, diese wird benoetigt um sicherzustellen damit dies keine automatische Anfrage ist, sondern von einen Menschen stammt! Vielen Dank fuer dein Verstaendniss. Bitte pruefe deine Eingaben!';
		default:
			return 'Unbekannter Fehler ist aufgetreten, bitte überprüfe deine Eingaben!';
	}
}

/**
  * Replacement for isset() and empty()
  * @since 0.2.4
  */
function valid_var($var)
{
	if (!isset($var) || !empty($var))
	{
		return false;
	}
	return $var;
}

function array_map_r( $func, $arr )
{
	$newArr = array();
	foreach( $arr as $key => $value )
	{
	$newArr[ $key ] = ( is_array( $value ) ? array_map_r( $func, $value ) : ( is_array($func) ? call_user_func_array($func, $value) : $func( $value ) ) );
	}
	return $newArr;
}
?>