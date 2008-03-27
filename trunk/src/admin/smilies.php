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
* @version    CVS: $Id: smilies.php 20 2008-03-27 14:02:56Z kwhark $
* @link       http://www.simlau.net/
*/
//
// Konstanten
//
define('GUESTBOOK', true);
define('ADMIN_PAGE', true);
define('REQUIRED_AUTH_LEVEL', 2);

//
// Dateien includieren
//
$root_dir = '../';
include_once $root_dir . "includes/common.php";

page_header('Smilie Verwaltung');

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'import':
		if (isset($_POST['file']))
		{
			load_smilie_pack($_POST['file']);
			message_die($lang['smilies_export_submit'], $lang['smilies_export_submit_desc']);
		}
		else
		{
			message_die($lang['guestbook_error'], 'var nicht gesetzt, daher abbruch.');
		}
	break;
	case 'export':
		$smilies_file = (isset($_POST['file'])) ? $_POST['file'] : '';
		$export_file = $root_dir . 'includes/store/smilies/' . $smilies_file;

		if (!is_writeable($export_file))
		{
			message_die($lang['guestbook_error'], sprintf($lang['smilies_export_file'], $export_file, '<a href="' . PAGE_ADMIN_SMILIES . '">', '</a>', '<a href="' . PAGE_ADMIN_INDEX . '">', '</a>'));
		}

		file_put_contents($export_file, generate_smilie_pack());

		message_die($lang['smilies_export_submit'], sprintf($lang['smilies_export_submit_desc'], $export_file, '<a href="' . PAGE_ADMIN_SMILIES . '">', '</a>', '<a href="' . PAGE_ADMIN_INDEX . '">', '</a>'));
	break;
	case 'edit':
		if (isset($_POST['submit']) && !empty($_POST['submit']))
		{
			$sql = 'UPDATE ' . SMILIES_TABLE . '
					SET `smilies_code` = ' . $db->sql_escape($_POST['smilies_code']) . ',
					    `smilies_url` = ' . $db->sql_escape($_POST['smilies_url']) . ',
					    `smilies_name` = ' . $db->sql_escape($_POST['smilies_name']) . '
				WHERE smilies_id = ' . $db->sql_escape($_POST['smilies_id']) . '
					LIMIT 1';
			$db->sql_query($sql) or die(var_dump($db->sql_error()));
			
			message_die('Smilie wurde geändert!', 'Smilie wurde geändert!');
		}
		
		$smilies = (isset($_GET['smilies'])) ? $_GET['smilies'] : "";
		
		if (empty($smilies))
		{
			die('foo');
		}

		$template->set_filenames(array(
			'body' => 'smilies_edit_body.html',
		));

		if (is_array($smilies) && count($smilies) > 1)
		{
			$sql_where_statement = '';
			foreach ($smilies as $smilie_id)
			{
				if (empty($sql_where_statement))
				{
					$sql_where_statement = 'WHERE smilies_id = ' . $db->sql_escape($smilie_id) . ' ';
				}
				else
				{
					$sql_where_statement .= 'OR smilies_id = ' . $db->sql_escape($smilie_id) . ' ';
				}
			}
			$sql = 'SELECT *
				FROM ' . SMILIES_TABLE . '
				' . $sql_where_statement;
		}
		else
		{
			$sql = 'SELECT *
				FROM ' . SMILIES_TABLE . '
				WHERE smilies_id = ' . $db->sql_escape($smilies);
		}

		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('edit_smilies', array(
				'ID' => $row['smilies_id'],
				'CODE' => $row['smilies_code'],
				'URL' => $row['smilies_url'],
				'NAME' => $row['smilies_name'],
			));
		}
		
		$template->pparse('body');
	break;
	default:
		$template->set_filenames(array(
			'body' => 'smilies_body.html',
		));

		$sql = 'SELECT smilies_id, smilies_code, smilies_url, smilies_name
		FROM ' . SMILIES_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('smilies', array(
				'ID' => $row['smilies_id'],
				'CODE' => $row['smilies_code'],
				'URL' => $root_dir . $config_table['smilies_path'] . '/' .$row['smilies_url'],
				'NAME' => $row['smilies_name'],
			));
		}

		$directory = read_directory($root_dir . 'includes/store/smilies/');
		$smilie_packs = array();
		foreach ($directory['file'] as $key => $filename)
		{
			if(preg_match('/\.(pak)$/', $filename))
			{
				$smilie_packs[] = $filename;
			}
		}

		foreach ($smilie_packs as $smilie_pack_name)
		{
			$template->assign_block_vars('smilie_packs', array(
				'FILENAME' => $smilie_pack_name,
			));
		}

		$template->pparse('body');
}

include_once $root_dir . 'includes/footer.php';

?>
