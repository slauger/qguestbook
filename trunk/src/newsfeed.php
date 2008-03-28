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
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: newsfeed.php 15 2008-03-27 13:35:32Z kwhark $
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true);
$root_dir = './';
include_once $root_dir . 'includes/common.php';

if (!$config->get('newsfeed')) {
	message_die('RSS Feed nicht verfügbar!', 'Diese Funktion wurde vom Administrator deaktiviert!');
}

header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Type: text/xml; charset=' . $config->get('charset'));

$sql = 'SELECT posts_id, posts_name, posts_email, posts_ip, posts_www, posts_icq, posts_text, posts_date, posts_active, posts_hide_email, posts_marked
	FROM ' . POSTS_TABLE . '
	WHERE posts_active = ' . $db->sql_escape(POST_ACTIVE) . '
	ORDER BY posts_id ' . strtoupper($config->get('postorder')) . '
	LIMIT ' . $db->sql_escape($config->get('rss_limit'));
$result = $db->sql_query($sql);

$template->set_filenames(array(
	'body' => 'newsfeed_main.html',
));

$template->assign_vars(array(
	'ENCODING' => $config->get('charset'),
	'GENERATOR' => 'qGuestbook',
	'TITLE' => $config->get('sitename'),
	'LINK' => real_path(),
	'LANGUAGE' => $config->get('language'),
	'DESCRIPTION' => $config-get('description'),
));

while ($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('switch_newsfeed', array(
		'ITEM_TITLE' => sprintf('Gästebucheintrag von %s', $encode->encode_html($row['posts_name'])),
		'ITEM_DATE' => date('r', $row['posts_date']),
		'ITEM_LINK' => real_path() . PAGE_INDEX . '?start=' . $row['posts_id'],
		'ITEM_GUID' => real_path() . PAGE_INDEX . '?start=' . $row['posts_id'],
		'ITEM_COMMENTS' => real_path() . PAGE_INDEX . '?start=' . $row['posts_id'],
		'ITEM_DESCRIPTION' => bbcode($row['posts_text']),
	));
}

$template->pparse('body');

?>