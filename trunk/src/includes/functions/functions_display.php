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
* @version    CVS: $Id: functions_display.php 20 2008-03-27 14:02:56Z kwhark $
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Hacking attempt');
	exit;
}

function page_title($title)
{
	global $template;
	$template->assign_vars(array(
		'PAGE_TITLE' => $title,
	));
}

function page_header($page_title = '')
{
	global $template, $config_table, $db;
	global $root_dir, $microtime, $lang, $encode;

	// Der Header wurde geparst.
	define('HEADER_INC', true);

	$microtime = site_microtime();

	$template->set_filenames(array(
		'header' => 'overall_header.html',
	));

	if (empty($page_title))
	{
		$page_title = 'Information';
	}

	page_title($page_title);

	if ($config_table['newsfeed'] == 1)
	{
		$template->assign_block_vars('newsfeed', array());
	}
	
	// Added in 0.2.4
	$sql = 'SELECT meta_name, meta_content
		FROM ' . META_TABLE;
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('meta_tags', array(
			'NAME' => $encode->encode_html($row['meta_name']),
			'CONTENT' => $encode->encode_html($row['meta_content']),
		));
	}

	if (defined('ADMIN_PAGE'))
	{
		$template->assign_vars(array(
			'U_ADMIN_MODERATE' => PAGE_ADMIN_MODERATE,
			'U_ADMIN_INDEX' => PAGE_ADMIN_INDEX,
			'U_ADMIN_USERS' => PAGE_ADMIN_USERS,
			'U_ADMIN_DATABASE' => PAGE_ADMIN_DATABASE,
			'U_ADMIN_SETTIGNS' => PAGE_ADMIN_SETTINGS,
			'U_ADMIN_SMILIES' => PAGE_ADMIN_SMILIES,
			'U_ADMIN_EMAIL' => PAGE_ADMIN_EMAIL,
			'U_ADMIN_INDEX' => PAGE_ADMIN_INDEX,
			'U_ADMIN_META' => PAGE_ADMIN_META,
			'U_ADMIN_LOGIN' => PAGE_LOGIN,
			'U_ADMIN_LOGOUT' => PAGE_ADMIN_LOGOUT,
		));
	}

	$template->assign_vars(array(
		'ROOT_DIR' => $root_dir,

		'DIRECTION' => $lang['DIRECTION'],
		'USER_LANG' => $lang['USER_LANG'],

		'CHARSET' => $config_table['charset'],
		'SITENAME' => $config_table['sitename'],
		'SITE_DESC' => $config_table['description'],
		'SCRIPT_URL' => real_path(),

		'L_PAGE_ADMIN_INDEX' => 'Moderatoren Bereich',

		'U_PAGE_ADMIN_INDEX' => PAGE_ADMIN_INDEX,
		'U_NEWSFEED' => PAGE_NEWSFEED,
		'U_INDEX' => PAGE_INDEX,
		'U_POSTING' => PAGE_POSTING,
	));

	$template->pparse('header');

}

function page_footer()
{
	global $template, $config_table, $db;
	global $root_dir, $microtime, $lang;

	$template->set_filenames(array(
		'footer' => 'overall_footer.html',
	));


	// Speicherauslastung des Scripts
	if (function_exists('memory_get_usage'))
	{
		if (isset($config_table['debug_info']) && $config_table['debug_info'] == 1) {
			$memory_usage = @memory_get_usage();
			switch ($memory_usage)
			{
				case $memory_usage >= 1048576:
					$memory_usage = ' | Memory Usage: ' . round((round($memory_usage / 1048576 * 100) / 100), 2) . ' MB';
				break;
				case $memory_usage >= 1024:
					$memory_usage = ' | Memory Usage: ' . round((round($memory_usage / 1024 * 100) / 100), 2) . ' KB';
				break;
				default:
					$memory_usage = ' | Memory Usage: ' . $memory_usage . ' Bytes';
				break;
			}
		}
		else
		{
			$memory_usage = '';
		}
	}
	else
	{
		$memory_usage = '';
	}

	// Soll der Link zur Administration angezeigt werden?
	$admin_link = ($config_table['admin_link']) ? sprintf("<a href=\"%s\">%s</a><br />", PAGE_ADMIN_INDEX, 'Moderatoren Bereich') : "";

	// Mikrozeit ausrechnen
	$microtime = site_microtime_calc($microtime, site_microtime());

	// Die komplette Debug Information
	$debug_info = '[ Time: '.$microtime.' Sec | ' . $db->sql_num_queries() . ' Queries ' . $memory_usage . ' ]';

	if ($config_table['debug_info'] == 1)
	{
		$template->assign_block_vars('debug_info', array());
	}

	$template->assign_vars(array(
		'DEBUG_INFO' => $debug_info,
		'VERSION' => $config_table['version'],
		'ADMIN_LINK' => $admin_link,
		'MEMORY_USAGE' => $memory_usage,
	));

	$template->pparse('footer');

	// Die Datenbankverbindung schließen wir vorher.
	$db->sql_close();

	// Gut, somit wären wir ja wohl fertig.
	if (!defined('DEBUG_EXTRA'))
	{
		die();
		exit;
	}
}

?>