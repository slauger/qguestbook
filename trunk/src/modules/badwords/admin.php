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
* @version    CVS: $Id: badwords.php 82 2008-07-10 12:27:15Z kwhark $
* @link       http://www.simlau.net/
*/
if (!defined('GUESTBOOK')) {
	die('Access denied!');
}

$module_path = dirname(__FILE__);
page_header('Badwords Verwaltung');

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : '';

switch ($mode)
{
	case 'edit':
		die('Noch nicht fertig  :)');
		
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
		if (!$globals->get('badwords')) {
			message_die('foo', 'bar');
		}
		
		if (is_array($globals->get('badwords')) && count($globals->get('badwords')) > 1)
		{
			$sql_where_statement = '';
			foreach ($globals->get('badwords') as $smilie_id)
			{
				if (empty($sql_where_statement))
				{
					$sql_where_statement = 'WHERE words_id = ' . $db->sql_escape($smilie_id) . ' ';
				}
				else
				{
					$sql_where_statement .= 'OR words_id = ' . $db->sql_escape($smilie_id) . ' ';
				}
			}
			$sql = 'DELETE FROM ' . WORDS_TABLE . '
					' . $sql_where_statement . '
				LIMIT ' . count($sql_where_statement);
		}
		else
		{
			$sql = 'DELETE FROM ' . WORDS_TABLE . '
					WHERE words_id = ' . $db->sql_escape($globals->get('badwords')) . '
				LIMIT 1';
		}
		
		if (!$db->sql_query($sql)) {
			message_die('Fehler', 'Es war etwas falsch');
		}
		
		message_die('Erolgreich gelöscht', 'Erfolgreich gelöscht!');
	break;
	default:
		$template->set_filenames(array(
			'body' => $module_path.'/template/badwords_body.html',
		));

		$sql = 'SELECT words_id, words_name, words_replacement
				FROM ' . WORDS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result)) {
			$template->assign_block_vars('badwords', array(
				'ID' => $row['words_id'],
				'NAME' => $row['words_name'],
				'REPLACEMENT' => $row['words_replacement'],
			));
		}

		$template->pparse('body');
	break;
}

page_footer();

?>
