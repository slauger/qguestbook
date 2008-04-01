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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

define('GUESTBOOK', true); // Wird immer gebraucht...
define('ADMIN_PAGE', true); // Dieses Seite ist eine Moderatoren-Seite
define('REQUIRED_AUTH_LEVEL', 2); // Welches Userlevel wird hier gebraucht?

$root_dir = './';
include_once $root_dir . 'includes/common.php';

page_header("Seitentitel");

$sql = 'SELECT COUNT(´posts_id´)
	FROM ' . POSTS_TABLE;

$template->set_filenames(array(
	'body' => 'example_body.html',
	'another_body' => 'another_exampe.html',
));

if (!$config->get('bar')) {
	message_die('config bar is empty or not set', 'yes, it\'s true');
}

if (!$db->sql_query($sql)) {
	$error = $db->sql_error();
	message_die(sprintf($lang['sql_error'], $error['code']), sprintf($lang['sql_error_explain'], $error['error'], $error['sql'], __FILE__, __LINE__));
}

$template->pparse('index');

page_footer();

?>