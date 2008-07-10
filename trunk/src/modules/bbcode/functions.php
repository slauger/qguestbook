<?php
/**
* qGuestbook BBCode Module
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
* @subpackage BBCode
* @author     Simon Lauger <admin@simlau.net>
* @copyright  2007-2008 Simon Lauger
* @license    http://www.gnu.org/licenses/gpl.html GNU GPL 3.0
* @version    CVS: $Id$
* @link       http://www.simlau.net/
*/

function convertlinebreaks ($text)
{
	return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}

function bbcode_stripcontents ($text)
{
	return preg_replace ("/[^\n]/", '', $text);
}

function bbcode_url ($action, $attributes, $content, $params, $node_object)
{
	if ($action == 'validate')
	{
		return true;
	}
		if (!isset ($attributes['default']))
	{
		return '<a href="'.$content.'">'.$content.'</a>';
	}
	return '<a href="'.$attributes['default'].'">'.$content.'</a>';
}

function bbcode_image ($action, $attributes, $content, $params, $node_object)
{
	global $encode;
	if ($action == 'validate')
	{
		return true;
	}
	if (!isset($content) || empty($content))
	{
		// Hat der User das Attribut falsch angegeben?
		if (isset($attributes['default']) && !empty($attributes['default']))
		{
			$content = $attributes['default'];
		}
		else
		{
			return "[img][/img]";
		}
	}
	
	$disallowed = array('javascript:', 'file:', 'data:', 'jar:');
	foreach ($disallowed as $string)
	{
		// XXS gibts hier nicht!
		if (preg_match("/$string/i", $content, $matches))
		{
			return "[img]".$encode->encode_html($content)."[/img]";
		}
			
	}
	return '<img src="'.$encode->encode_html($content).'" alt="" />';
}	


function bbcode_quote ($action, $attributes, $content, $params, &$node_object) {
	if ($action == 'validate')
	{
		return true;
	}
	$quote = isset($attributes['default']) ? $attributes['default'] . " hat folgendes geschrieben:" : "Zitat:";
	return "<table width=\"90%\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\" align=\"center\">
			<tr>
				<td><span class=\"genmed\"><strong>" . $attributes['default'] . " hat folgendes geschrieben:</strong></span></td>
			</tr>
			<tr>
				<td class=\"quote\">" . $content . "</td>
			</tr>
		</table>";
}

?>