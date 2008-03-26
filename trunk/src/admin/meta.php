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

/**
 * Benötigte Konstanten
 */
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 2);

//
// Dateien includieren
//
$root_dir = '../';
include_once $root_dir . "includes/common.php";
include_once $root_dir . "includes/header.php";

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';
$update = (isset($_POST['update'])) ? $_POST['update'] : '';

switch ($mode)
{
	case 'add':
		$name = (isset($_POST['name'])) ? $_POST['name'] : '';
		$content = (isset($_POST['content'])) ? $_POST['content'] : '';

		if (empty($name) || empty($content))
		{
			message_die('bla', 'foo');
		}

		if (!empty($update))
		{
			$sql = 'UPDATE ' . META_TABLE . '
					SET meta_content = ' . $db->sql_escape($_POST['content']) . '
					WHERE meta_name = ' . $db->sql_escape($_POST['name']) . '
				LIMIT 1';
		}
		else
		{
			$sql = 'INSERT INTO ' . META_TABLE . '
					(meta_name , meta_content)
				VALUES (' . $db->sql_escape($name) . ', ' . $db->sql_escape($content) . ');';
		}

		$result = $db->sql_query($sql);

		message_die('erfolg!', 'lol :D');
	break;
	case 'edit':
		$template->set_filenames(array(
			'body' => 'meta_body.html',
		));

		$sql = 'SELECT meta_name, meta_content
			FROM ' . META_TABLE . '
			WHERE meta_name = ' . $db->sql_escape($_GET['t']);
		$result = $db->sql_query($sql);

		$row = $db->sql_fetchrow($result);

		$template->assign_vars(array(
			'DISSABLED' =>  "disabled=\"true\"",
			'NAME' => $row['meta_name'],
			'CONTENT' => $row['meta_content'],
		));

		$template->assign_block_vars('switch_edit', array());

		$template->pparse('body');
	break;
	default:
		$template->set_filenames(array(
			'body' => 'meta_body.html',
		));

		$template->assign_block_vars('list', array());


		$sql = 'SELECT meta_name, meta_content
			FROM ' . META_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('list.meta_tags', array(
				'NAME' => decode_html($row['meta_name']),
				'CONTENT' => decode_html($row['meta_content']),
			));
		}

		$template->pparse('body');
	break;
}

include_once $root_dir . 'includes/footer.php';

?>