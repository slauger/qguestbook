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
* @version    0.2.4
* @link       http://www.simlau.net/
*/

function load_smilie_pack($filename)
{
	$smilie_pack = file($filename);
	foreach ($smilie_pack as $line)
	{
		$smilie = explode('#', $line);
		$smilies[] = array(
			'code' => $smilie[0],
			'url' => $smilie[1],
			'name' => $smilie[2],
		);
	}
	foreach ($smilies as $key => $value)
	{
		$sql = 'INSERT INTO ' . SMILIES_TABLE . '
		smilies_id, smilies_code, smilies_url, smilies_name
		VALUES (' . $db->sql_escape('') . ', ' . $db->sql_escape($smilies[$key]['code']) . ', ' . $db->sql_escape($smilies[$key]['url']) . ', ' . $db->sql_escape($smilies[$key]['name']);
		$result = $db->sql_query($sql);
	}
}

function generate_smilie_pack()
{
	global $db;

	$sql = 'SELECT smilies_id, smilies_code, smilies_url, smilies_name
		FROM ' . SMILIES_TABLE;
	$result = $db->sql_query($sql);

	$smilie_pack = '';

	while ($row = $db->sql_fetchrow($result))
	{
		$smilie_pack .= $row['smilies_code'] . "#" . $row['smilies_url'] . "#" . $row['smilies_name'] . "\n";
	}

	return $smilie_pack;
}

/**
 * Gibt ein Array mit den Dateinamen in $directory zurück
 */
function read_directory($directory = '.')
{
	if (!is_dir($directory))
	{
		return false;
	}

	$work = opendir($directory);
	$ignore = array('.', '..');
	$files = array();

	while (false !== ($file = readdir($work)))
	{
		if (!in_array($file, $ignore))
		{
			if (filetype($directory . $file) == 'dir')
			{
				$files['dir'][] = $file;
			}
			else
			{
				$files['file'][] = $file;
			}
		}
	}
	closedir($work);
	return $files;
}

function clean_database($other_tables = array())
{
	global $db;

	$tables = array(
		CONFIG_TABLE,
		LANGUAGE_TABLE,
		STYLES_TABLE,
		DISSALOW_TABLE,
		POSTS_TABLE,
		USERS_TABLE,
		SMILIES_TABLE,
	);
	$querys = array();
	$count = count($tables) + count($other_tables);

	foreach ($tables as $table)
	{
		$querys[] = 'OPTIMIZE TABLE ' . $table;
	}

	if (!empty($other_tables))
	{
		foreach ($other_tables as $table)
		{
			$querys[] = 'OPTIMIZE TABLE ' . $table;
		}
	}

	foreach ($querys as $sql)
	{
		$db->sql_query($sql);
	}

	return $count;
}

function costum_phpinfo()
{
	ob_start();
	@phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES);
	$phpinfo = ob_get_clean();

	$phpinfo = trim($phpinfo);

	// Here we play around a little with the PHP Info HTML to try and stylise
	// it along phpBB's lines ... hopefully without breaking anything. The idea
	// for this was nabbed from the PHP annotated manual
	preg_match_all('#<body[^>]*>(.*)</body>#si', $phpinfo, $output);

	if (empty($phpinfo) || empty($output))
	{
		# message_die();
	}

	$output = $output[1][0];
	$output = preg_replace('#<tr class="v"><td>(.*?<a[^>]*><img[^>]*></a>)(.*?)</td></tr>#s', '<tr class="row1"><td><table class="type2"><tr><td>\2</td><td>\1</td></tr></table></td></tr>', $output);
	$output = preg_replace('#<table[^>]+>#i', '<table border="0" cellpadding="3" cellspacing="1" align="center" width="80%" class="headline">', $output);
	$output = preg_replace('#<img border="0"#i', '<img', $output);
	$output = str_replace(array('class="e"', 'class="v"', 'class="h"', '<hr />', '<font', '</font>'), array('class="row1"', 'class="row2"', '', '', '<span', '</span>'), $output);

	preg_match_all('#<div class="center">(.*)</div>#siU', $output, $output);
	$output = $output[1][0];

	return $output;
}

//
// Kleines Workaround, wär aber eigentlich
// die wunderbare Aufgabe von qLanguage ;D
//
function parse_language($language)
{
	global $root_dir;
	$filename = "{$root_dir}includes/language/{$language}/info.txt";
	$content = file_get_contents($filename);
	return $content;
}

?>