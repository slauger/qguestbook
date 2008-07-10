<?php
/**
* qGuestbook textLimiter Module
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
* @subpackage textLimiter
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id: index.php 72 2008-06-22 01:00:47Z kwhark $
* @link       http://www.simlau.net/
*/

class TextLimiter
{
	public function stringLimit($text, $limit)
	{
		global $config;
		if (is_numeric($limit) && $limit > 0) {
			if (preg_match_all('#(\b\w{'.$config->get('max_lenght').',}\b)#', $text, $matches)) {
				foreach ($matches[0] as $cut_word) {
					$text = str_replace($cut_word, substr($cut_word, 0, -(strlen($cut_word) - $limit)) . '... ', $text);
				}
			}
		}
		return $text;
	}
	
	public function wordLimit($text, $limit)
	{
		return $text;
	}

	public function on_viewposts_second()
	{
		global $row, $config;
		// Maximale Zeichen per Wort begrenzen
		if ($config->get('string_limit') && $config->get('string_limit') != 0) {
			$row['posts_text'] = $this->stringLimit($row['posts_text']);
		}
		// Maximale Anzahl an Wörtern begrenzen
		if ($config->get('word_limit') && $config->get('word_limit') != 0) {
			$row['posts_text'] = $this->wordLimit($row['posts_text']);
		}
	}
}

?>