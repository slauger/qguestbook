<?php
/**
* qGuestbook Badwords Module
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
* @subpackage Badwords
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: index.php 72 2008-06-22 01:00:47Z kwhark $
* @link       http://www.simlau.net/
*/

class Badwords
{
	private $badwords;
	
	public function __construct()
	{
		global $config, $root_dir, $db;
		
		$this->badwords = array();
		
		$sql = 'SELECT words_id, words_name, words_replacement
				FROM ' . WORDS_TABLE;
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result)) {
			$this->badwords['words'][] = $row['words_name'];
			$this->badwords['replacements'][] = $row['words_replacement'];
		}
	}
	
	public function remove_badwords($text)
	{
		foreach ($this->badwords as $key => $value) {
			$text = str_replace($this->badwords['words'][$key], $this->badwords['replacements'][$key], $text);
		}
		return $text;
	}
	
	public function on_viewposts_second()
	{
		global $row;
		if (isset($row['posts_text']) && !empty($row['posts_text'])) {
			$row['posts_text'] = $this->remove_badwords($row['posts_text']);
		}
		if (isset($row['posts_name']) && !empty($row['posts_name'])) {
			$row['posts_name'] = $this->remove_badwords($row['posts_name']);
		}
	}
}

?>