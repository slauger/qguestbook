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
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

if (!defined('GUESTBOOK')) {
	die('Access denied!');
}

define('REQUIRED_AUTH_LEVEL', 2);

//
// Dateien includieren
//
echo basename(__FILE__);
include_once $root_dir . "includes/common.php";

page_header('Smilie Verwaltung');

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'import':
		if (!$globals->post('file')) {
			message_die($lang['ERROR_MAIN'], 'Du hast keine Datei angegeben, die du importieren willst!');
		} else {
			$import_file = sprintf("%sincludes/store/smilies/%s", $root_dir, $globals->post('file'));
			if (import_smiliepack($import_file, true)) {
				message_die('Smilie Paket wurde erfolgreich importiert!', 'Smilie Paket wurde erfolgreich importiert!');
			} else {
				message_die($lang['ERROR_MAIN'], 'Konnte Paket nicht importieren!');
			}
		}
	break;
	case 'export':
		if (!$globals->post('file')) {
			message_die($lang['ERROR_MAIN'], 'Du musst eine Datei angeben, in die du exportieren willst!');
		}
	
		$export_file = sprintf("%sincludes/store/smilies/%s", $root_dir, $globals->post('file'));

		if (!@file_put_contents($export_file, export_smiliepack())) {
			message_die($lang['ERROR_MAIN'], sprintf($lang['smilies_export_file'], $encode->encode_html($globals->post('file')), '<a href="' . PAGE_ADMIN_SMILIES . '">', '</a>', '<a href="' . PAGE_ADMIN_INDEX . '">', '</a>'));
		}

		message_die($lang['smilies_export_submit'], sprintf($lang['smilies_export_submit_desc'], $encode->encode_html($globals->post('file')), '<a href="' . PAGE_ADMIN_SMILIES . '">', '</a>', '<a href="' . PAGE_ADMIN_INDEX . '">', '</a>'));
	break;
	case 'edit':
		if ($globals->post('submit')) {
			$sql = 'UPDATE ' . SMILIES_TABLE . '
					SET `smilies_code` = ' . $db->sql_escape($_POST['smilies_code']) . ',
					    `smilies_url` = ' . $db->sql_escape($_POST['smilies_url']) . ',
					    `smilies_name` = ' . $db->sql_escape($_POST['smilies_name']) . '
				WHERE smilies_id = ' . $db->sql_escape($_POST['smilies_id']) . '
					LIMIT 1';
			$db->sql_query($sql);
			
			message_die('Smilie wurde geändert!', 'Smilie wurde geändert!');
		}
				
		if (!$globals->get('smilies')) {
			message_die('Du hast keine ID angegeben die du bearbeiten willst', 'Du hast keine ID angegeben die du bearbeiten willst');
		}

		$template->set_filenames(array(
			'body' => 'smilies_edit_body.html',
		));

		if (is_array($globals->get('smilies')) && count($globals->get('smilies')) > 1)
		{
			$sql_where_statement = '';
			foreach ($globals->get('smilies') as $smilie_id)
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
				WHERE smilies_id = ' . $db->sql_escape($globals->get('smilies'));
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
	case 'delete':
		if (!$globals->get('smilies')) {
			message_die('foo', 'bar');
		}
		
		if (is_array($globals->get('smilies')) && count($globals->get('smilies')) > 1)
		{
			$sql_where_statement = '';
			foreach ($globals->get('smilies') as $smilie_id)
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
			$sql = 'DELETE FROM ' . SMILIES_TABLE . '
					' . $sql_where_statement . '
				LIMIT ' . count($sql_where_statement);
		}
		else
		{
			$sql = 'DELETE FROM ' . SMILIES_TABLE . '
					WHERE smilies_id = ' . $db->sql_escape($globals->get('smilies')) . '
				LIMIT 1';
		}
		
		if (!$db->sql_query($sql)) {
			message_die('nope', 'was falsch');
		}
		
		message_die('Erolgreich gelöscht', 'Erfolgreich gelöscht!');
	break;
	default:
		$template->set_filenames(array(
			'body' => 'smilies_body.html',
		));

		$sql = 'SELECT smilies_id, smilies_code, smilies_url, smilies_name
		FROM ' . SMILIES_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result)) {
			$template->assign_block_vars('smilies', array(
				'ID' => $row['smilies_id'],
				'CODE' => $row['smilies_code'],
				'URL' => $root_dir . $config_table['smilies_path'] . '/' .$row['smilies_url'],
				'NAME' => $row['smilies_name'],
			));
		}

		$directory = read_directory($root_dir . 'includes/store/smilies/');
		$smilie_packs = array();
		foreach ($directory['file'] as $key => $filename) {
			if(preg_match('/\.(pak)$/', $filename)) {
				$smilie_packs[] = $filename;
			}
		}

		foreach ($smilie_packs as $smilie_pack_name) {
			$template->assign_block_vars('smilie_packs', array(
				'FILENAME' => $smilie_pack_name,
			));
		}

		$template->pparse('body');
	break;
}

include_once $root_dir . 'includes/footer.php';

?>
