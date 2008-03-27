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
* @version    CVS: $Id: popup.php 15 2008-03-27 13:35:32Z kwhark $
* @link       http://www.simlau.net/
*/
//
// Konstanten
//
define('GUESTBOOK', true);

//
// Dateien includieren
//
$root_dir = "./";
include_once $root_dir . 'includes/common.php';

$mode = (!isset($_GET['mode']) || empty($_GET['mode'])) ? '' : $_GET['mode'];

switch ($mode)
{
	case 'smilies':

		$sql = 'SELECT smilies_id, smilies_code, smilies_url, smilies_name
			FROM ' . SMILIES_TABLE;
		$result = $db->sql_query($sql);

		$template->set_filenames(array(
			'index' => 'smilies_body.html',
		));

		while ($row = $db->sql_fetchrow($result))
		{
			$template->assign_block_vars('smilies', array(
				'ID' => $row['smilies_id'],
				'CODE' => $row['smilies_code'],
				'URL' => real_path() . $config_table['smilies_path'] . $row['smilies_url'],
				'NAME' => $row['smilies_name'],
			));
		}

		$template->assign_vars(array(
			'CHARSET' => $config_table['charset'],
			'SITENAME' => $config_table['sitename'],
			'PAGE_TITLE' => $lang['smilies']
		));


		$template->pparse('index');
	break;
	case 'bbcodes':
		$template->set_filenames(array(
			'index' => 'bbcodes_body.html',
		));

		$template->assign_vars(array(
			'CHARSET' => $config_table['charset'],
			'SITENAME' => $config_table['sitename'],
			'PAGE_TITLE' => $lang['bbcodes'],
		));

		$template->pparse('index');
	break;
}

?>