<?php
/**
* qGuestbook Smilies Module
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
* @category   Module
* @package    Modules
* @subpackage Smilies
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

// Erweitert in 0.2.4... um export(), list() und import()
class smilies
{
	private $smilies;
	private $smilies_url;
	private $replace_str;
	
	public function __construct()
	{
		global $config, $root_dir, $db;
		$this->smilies = array();
		$this->smilies_url = real_path() . $config->get('smilies_path') . '%1s';
		$this->replace_str = '<img src="%1s" alt="%2s" border="0" />';
		
		$sql = 'SELECT smilies_id, smilies_code, smilies_url, smilies_name
			FROM ' . SMILIES_TABLE . '
			WHERE smilies_id <> ' . $db->sql_escape('');
		$result = $db->sql_query($sql);
		
		// Fixed in 0.2.4... etwas vergessen, was dazu
		// führte, dass immer nur der letzte Smilie im
		// Array geblieben ist. Nunja, ist jedenfalls behoben.
		while ($row = $db->sql_fetchrow($result))
		{
			$this->smilies[$row['smilies_id']] = array(
				'smilies_code' => $row['smilies_code'],
				'smilies_url'  => $row['smilies_url'],
				'smilies_name' => $row['smilies_name'],
			);
		}
	}
	
	public function make_smilies($text)
	{
		foreach ($this->smilies as $key => $value) {
			$text = str_replace($this->smilies[$key]['smilies_code'], sprintf($this->replace_str, sprintf($this->smilies_url, $this->smilies[$key]['smilies_url']), $this->smilies[$key]['smilies_name']), $text);
		}
		return $text;
	}
	
	public function on_viewposts_second()
	{
		global $row;
		if (isset($row['posts_text']) && !empty($row['posts_text'])) {
			$row['posts_text'] = $this->make_smilies($row['posts_text']);
		}
	}
	
	public function export($filename)
	{
		global $db;
		foreach ($this->smilies as $key => $value) {
			$smilie_pack .= $this->smilies[$key]['smilies_url'] . "=+" . $this->smilies[$key]['smilies_code'] . "=+" . $this->smilies[$key]['smilies_name'] . "\n";
		}
		file_put_contents($filename, $smilie_pack);
	}
	
	public function export_array()
	{
		return $this->smilies;
	}
	
	public function import($filename)
	{
		global $db;
		$smilie_pack = file($filename);
	
		foreach ($smilie_pack as $line) {
			$smilie = explode('=+', $line);
			$smilies[] = array(
				'code'  => $smilie[1],
				'url'   => $smilie[0],
				'name'  => str_replace("\n", "", $smilie[2]),
			);
		}
		
		$db->sql_query('TRUNCATE TABLE ' . SMILIES_TABLE);
		
		foreach ($smilies as $key => $value) {
			$sql = 'INSERT INTO ' . SMILIES_TABLE . '
				(smilies_id, smilies_code, smilies_url, smilies_name)
				VALUES (\'\', ' . $db->sql_escape($smilies[$key]['code']) . ', ' . $db->sql_escape($smilies[$key]['url']) . ', ' . $db->sql_escape($smilies[$key]['name']) . ')';
			$db->sql_query($sql);
		}
	}
}

?>